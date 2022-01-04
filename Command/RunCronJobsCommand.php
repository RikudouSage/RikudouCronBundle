<?php

namespace Rikudou\CronBundle\Command;

use DateTime;
use Rikudou\CronBundle\Cron\CronJobList;
use Rikudou\CronBundle\DTO\CronJobError;
use Rikudou\CronBundle\Event\CronEvents;
use Rikudou\CronBundle\Event\CronJobErrorEvent;
use Rikudou\CronBundle\Traits\OptionalLoggerTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Throwable;

final class RunCronJobsCommand extends Command
{
    use OptionalLoggerTrait;

    protected static $defaultName = 'cron:run';

    /**
     * @var CronJobList
     */
    private $cronJobList;

    /**
     * @var EventDispatcherInterface|null
     */
    private $eventDispatcher;

    public function __construct(CronJobList $cronJobList)
    {
        $this->cronJobList = $cronJobList;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Runs cron jobs that are due')
            ->addOption(
                'no-default-logging',
                null,
                InputOption::VALUE_NONE,
                'If this flag is present, no default logs (e.g. cron job start and end) will be logged. Your own logs will be logged normally.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $shouldLog = !$input->getOption("no-default-logging") && $this->logger !== null;
        $errors = [];

        foreach ($this->cronJobList->getDueJobs() as $cronJobName => $cronJob) {
            try {
                if ($shouldLog) {
                    $this->logger->info("[CRON] Executing cron job '${cronJobName}'");
                }
                $cronJob->execute($input, $output, $this->logger);
                if (!$shouldLog) {
                    $this->logger->info("[CRON] Cron job '${cronJobName}' successfully executed");
                }
            } catch (Throwable $exception) {
                if ($shouldLog) {
                    $this->logger->error("[CRON] Cron job '${cronJobName}' failed");
                    $this->logger->error("[CRON] {$exception->getMessage()}");
                    $this->logger->error("[CRON] {$exception->getTraceAsString()}");
                }
                $errors[$cronJobName] = new CronJobError($cronJobName, $exception, new DateTime());
            }
        }


        foreach ($this->cronJobList->getDueCommands() as $commandName => $command) {
            try {
                if ($shouldLog) {
                    $this->logger->info("[CRON] Executing command ${commandName}");
                }
                $command = $this->getApplication()->find($command);
                $commandInput = new ArrayInput([]);
                $exitCode = $command->run($commandInput, $output);
                if ($shouldLog) {
                    if ($exitCode === 0) {
                        $this->logger->info("[CRON] Command ${commandName} executed successfully");
                    } else {
                        $this->logger->warning("[CRON] Command ${commandName} exited with non-zero exit code: ${exitCode}");
                    }
                }
            } catch (Throwable $exception) {
                if ($shouldLog) {
                    $this->logger->error("[CRON] Execution of command ${commandName} failed");
                    $this->logger->error("[CRON] {$exception->getMessage()}");
                    $this->logger->error("[CRON] {$exception->getTraceAsString()}");
                }
                $errors[$commandName] = new CronJobError($commandName, $exception, new DateTime());
            }
        }

        if (count($errors)) {
            if ($this->eventDispatcher !== null) {
                $event = new CronJobErrorEvent($errors);
                $this->eventDispatcher->dispatch($event, CronEvents::ERROR);
            }
            return 1;
        }

        return 0;
    }

    public function setEventDispatcher(?EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }
}

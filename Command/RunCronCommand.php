<?php

namespace Rikudou\CronBundle\Command;

use Cron\CronExpression;
use Psr\Log\LoggerInterface;
use Rikudou\CronBundle\Cron\CronJobInterface;
use Rikudou\CronBundle\Cron\CronJobsList;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunCronCommand extends ContainerAwareCommand
{

    protected static $defaultName = "cron:run";

    public function __construct(?string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setDescription("Runs all cron jobs that are due");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cronJobsList = $this->getContainer()->get(CronJobsList::class);
        /** @var LoggerInterface $logger */
        $logger = $this->getContainer()->get("rikudou_cron.logger");

        $errors = [];
        $success = 0;
        foreach ($cronJobsList as $cronJob) {
            /** @var CronJobInterface $cronJob */
            try {
                $cronJob = new $cronJob;
                if (method_exists($cronJob, "isEnabled")) {
                    if (!$cronJob->isEnabled()) {
                        continue;
                    }
                }
                $cronExpression = CronExpression::factory($cronJob->getCronExpression());
                if ($cronExpression->isDue()) {
                    $logger->info("[CRON] Executing cron job from class " . get_class($cronJob));
                    $cronJob->execute($input, $output, $logger);
                    $logger->info("[CRON] Cron job " . get_class($cronJob) . " successfully executed");
                }
                $success++;
            } catch (\Throwable $exception) {
                $logger->error("[CRON] Cron job " . get_class($cronJob) . " failed");
                $logger->error("[CRON] {$exception->getMessage()}");
                $logger->error("[CRON] {$exception->getTraceAsString()}");
                if ($output->isVeryVerbose()) {
                    $error = $exception->getTraceAsString();
                } else if ($output->isVerbose()) {
                    $error = "{$exception->getMessage()} (file {$exception->getFile()} at line {$exception->getLine()})";
                } else {
                    $error = $exception->getMessage();
                }
                $errors[get_class($cronJob)] = $error;
            }
        }

        foreach ($cronJobsList->getCommands() as $command => $cron) {
            $commandName = $command;
            try {
                $cronExpression = CronExpression::factory($cron);
                if ($cronExpression->isDue()) {
                    $logger->info("[CRON] Executing command $commandName");
                    $command = $this->getApplication()->find($command);
                    $commandInput = new ArrayInput([]);
                    $exitCode = $command->run($commandInput, $output);
                    if($exitCode === 0) {
                        $logger->info("[CRON] Command $commandName executed successfully");
                    } else {
                        $logger->warning("[CRON] Command $commandName exited with non-zero exit code: $exitCode");
                    }
                }
            } catch (\Throwable $exception) {
                $logger->error("[CRON] Execution of command $commandName failed");
                $logger->error("[CRON] {$exception->getMessage()}");
                $logger->error("[CRON] {$exception->getTraceAsString()}");
                if ($output->isVeryVerbose()) {
                    $error = $exception->getTraceAsString();
                } else if ($output->isVerbose()) {
                    $error = "{$exception->getMessage()} (file {$exception->getFile()} at line {$exception->getLine()})";
                } else {
                    $error = $exception->getMessage();
                }
                $errors[$commandName] = $error;
            }
        }

        if (count($errors)) {
            $output->writeln("<error>Encountered these errors when running cron:</error>");
            if ($output->isVeryVerbose()) {
                foreach ($errors as $class => $error) {
                    $output->writeln("Error in cron job {$class}");
                    $output->writeln($error);
                }
            } else {
                $table = new Table($output);
                $table->setHeaders([
                    "Class/Command",
                    "Error"
                ]);
                foreach ($errors as $class => $error) {
                    $table->addRow([
                        $class,
                        $error
                    ]);
                }
                $table->render();
            }
            $output->writeln("<info>$success jobs ran successfully</info>");
            return 1;
        }
        return 0;
    }

}
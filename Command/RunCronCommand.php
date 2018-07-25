<?php

namespace Rikudou\CronBundle\Command;

use Cron\CronExpression;
use Psr\Log\LoggerInterface;
use Rikudou\CronBundle\Cron\CronJobInterface;
use Rikudou\CronBundle\Cron\CronJobsList;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunCronCommand extends Command
{

    protected static $defaultName = "cron:run";

    /**
     * @var CronJobsList
     */
    private $cronJobsList;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(?string $name = null, CronJobsList $cronJobsList, LoggerInterface $logger)
    {
        parent::__construct($name);
        $this->cronJobsList = $cronJobsList;
        $this->logger = $logger;
    }

    protected function configure()
    {
        $this->setDescription("Runs all cron jobs that are due");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $errors = [];
        $success = 0;
        foreach ($this->cronJobsList->getClasses() as $cronJob) {
            /** @var CronJobInterface $cronJob */
            try {
                $cronJob = new $cronJob;
                $cronExpression = CronExpression::factory($cronJob->getCronExpression());
                if ($cronExpression->isDue()) {
                    $cronJob->execute($input, $output, $this->logger);
                }
                $success++;
            } catch (\Throwable $exception) {
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
                    "Class",
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
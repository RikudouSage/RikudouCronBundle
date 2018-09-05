<?php

namespace Rikudou\CronBundle\Command;

use Cron\CronExpression;
use Rikudou\CronBundle\Cron\CronJobInterface;
use Rikudou\CronBundle\Cron\CronJobsList;
use Rikudou\CronBundle\Helper\CanUseInit;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCronCommand extends ContainerAwareCommand
{

    use CanUseInit;

    protected static $defaultName = "cron:list";

    protected function configure()
    {
        $this
            ->setDescription("Lists all classes that are registered as cron jobs");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->init();
        $cronJobsList = $this->getContainer()->get(CronJobsList::class);

        if (!count($cronJobsList) && !count($cronJobsList->getCommands())) {
            $output->writeln("There are no registered cron jobs.");
            return 0;
        }

        if(count($cronJobsList)) {
            $output->writeln("<info>Classes</info>");

            $table = new Table($output);
            $table->setHeaders([
                "Class",
                "Enabled",
                "Cron expression",
                "Is due",
                "Next run"
            ]);

            foreach ($cronJobsList as $class) {
                /** @var CronJobInterface $cronJob */
                $cronJob = $this->getContainer()->get($class);
                try {
                    $enabled = true;
                    if (method_exists($cronJob, "isEnabled")) {
                        $enabled = $cronJob->isEnabled();
                    }
                    $cronExpression = CronExpression::factory($cronJob->getCronExpression());
                    $table->addRow([
                        $class,
                        $enabled ? "yes" : "no",
                        $cronJob->getCronExpression(),
                        $cronExpression->isDue() ? "yes" : "no",
                        $cronExpression->getNextRunDate()->format("Y-m-d H:i:s")
                    ]);
                } catch (\InvalidArgumentException $exception) {
                    $table->addRow([
                        "<error>$class</error>",
                        "<error>" . ($enabled ? "yes" : "no") . "</error>",
                        "<error>{$cronJob->getCronExpression()}</error>",
                        "<error>Cron expression is invalid</error>",
                        "<error>Cron expression is invalid</error>"
                    ]);
                }
            }

            $table->render();
        }

        if(count($cronJobsList->getCommands())) {
            $output->writeln("<info>Commands</info>");

            $table = new Table($output);

            $table->setHeaders([
                "Command",
                "Cron expression",
                "Is due",
                "Next run"
            ]);

            foreach ($cronJobsList->getCommands() as $command => $cron) {
                $cronExpression = CronExpression::factory($cron);
                $table->addRow([
                    $command,
                    $cron,
                    $cronExpression->isDue() ? "yes" : "no",
                    $cronExpression->getNextRunDate()->format("Y-m-d H:i:s")
                ]);
            }

            $table->render();
        }

        return 0;
    }

}
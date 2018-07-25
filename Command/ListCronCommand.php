<?php

namespace Rikudou\CronBundle\Command;

use Cron\CronExpression;
use Rikudou\CronBundle\Cron\CronJobInterface;
use Rikudou\CronBundle\Cron\CronJobsList;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCronCommand extends Command
{

    protected static $defaultName = "cron:list";

    /**
     * @var CronJobsList
     */
    private $cronJobsList;

    public function __construct(?string $name = null, CronJobsList $cronJobsList)
    {
        parent::__construct($name);
        $this->cronJobsList = $cronJobsList;
    }

    protected function configure()
    {
        $this
            ->setDescription("Lists all classes that are registered as cron jobs");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!count($this->cronJobsList)) {
            $output->writeln("There are no registered cron jobs.");
            return 0;
        }
        $table = new Table($output);
        $table->setHeaders([
            "Class",
            "Cron expression",
            "Is due",
            "Next run"
        ]);

        foreach ($this->cronJobsList as $class) {
            /** @var CronJobInterface $cronJob */
            $cronJob = new $class;
            try {
                $cronExpression = CronExpression::factory($cronJob->getCronExpression());
                $table->addRow([
                    $class,
                    $cronJob->getCronExpression(),
                    $cronExpression->isDue() ? "yes" : "no",
                    $cronExpression->getNextRunDate()->format("Y-m-d H:i:s")
                ]);
            } catch (\InvalidArgumentException $exception) {
                $table->addRow([
                    "<error>$class</error>",
                    "<error>{$cronJob->getCronExpression()}</error>",
                    "<error>Cron expression is invalid</error>",
                    "<error>Cron expression is invalid</error>"
                ]);
            }
        }

        $table->render();
    }

}
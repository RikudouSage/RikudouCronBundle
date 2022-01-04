<?php

namespace Rikudou\CronBundle\Command;

use Rikudou\CronBundle\Cron\CronJobList;
use Rikudou\CronBundle\Traits\OptionalLoggerTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ExecuteCronJobCommand extends Command
{
    use OptionalLoggerTrait;

    protected static $defaultName = 'cron:execute';

    /**
     * @var CronJobList
     */
    private $cronJobList;

    public function __construct(CronJobList $cronJobList)
    {
        $this->cronJobList = $cronJobList;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription("Executes a single cron job (even if it's disabled or is not due)")
            ->addArgument(
                'cronJobName',
                InputArgument::REQUIRED,
                'The cron job name, can be either a custom cron job name or the fully qualified class name'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $cronJobName = $input->getArgument('cronJobName');
        $cronJob = $this->cronJobList->findByName($cronJobName);

        if ($cronJob === null) {
            $output->writeln("Cron job with the name '${cronJobName}' does not exist. Try running cron:list.");
            return 1;
        }

        $cronJob->execute($input, $output, $this->logger);

        return 0;
    }
}

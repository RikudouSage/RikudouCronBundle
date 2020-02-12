<?php

namespace Rikudou\CronBundle\Command;

use Psr\Log\LoggerInterface;
use Rikudou\CronBundle\Cron\CronJobInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExecuteCronCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'cron:execute';

    protected function configure()
    {
        $this
            ->setDescription('Executes the cron command. Useful for testing')
            ->addArgument(
                'className',
                InputArgument::REQUIRED,
                'Fully qualified class name of the cron job'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var LoggerInterface $logger */
        $logger = $this->getContainer()->get("rikudou_cron.logger");
        /** @var CronJobInterface $cronJob */
        $cronJob = $this->getContainer()->get($input->getArgument('className'));

        $cronJob->execute($input, $output, $logger);
    }
}

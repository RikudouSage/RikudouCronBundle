<?php

namespace Rikudou\CronBundle\Cron;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface CronJobInterface
{

    /**
     * The cron expression for triggering this cron job
     * @return string
     */
    public function getCronExpression(): string;

    /**
     * This method will be executed when cron job runs
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param LoggerInterface $logger
     * @return mixed
     */
    public function execute(InputInterface $input, OutputInterface $output, LoggerInterface $logger);

}
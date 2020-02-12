<?php

namespace Rikudou\CronBundle\Cron;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface CronJobInterface
{
    /**
     * Returns the cron expression, e.g. '0 * * * *'
     *
     * @return string
     */
    public function getCronExpression(): string;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param LoggerInterface|null $logger
     * @return mixed
     */
    public function execute(InputInterface $input, OutputInterface $output, ?LoggerInterface $logger);
}

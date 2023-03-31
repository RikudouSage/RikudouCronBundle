<?php

namespace Rikudou\CronBundle\Cron;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface CronJobInterface
{
    /**
     * Returns the cron expression, e.g. '0 * * * *'
     */
    public function getCronExpression(): string;

    public function execute(InputInterface $input, OutputInterface $output, ?LoggerInterface $logger): void;
}

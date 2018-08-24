<?php

namespace Rikudou\CronBundle\Cron;

interface DisablableCronJobInterface
{

    /**
     * Returns true if cron job is enabled and should be ran, false otherwise
     * @return bool
     */
    public function isEnabled(): bool;

}
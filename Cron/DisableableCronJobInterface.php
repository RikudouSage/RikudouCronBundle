<?php

namespace Rikudou\CronBundle\Cron;

interface DisableableCronJobInterface extends CronJobInterface
{
    /**
     * Whether the cron job is disabled or not
     */
    public function isEnabled(): bool;
}

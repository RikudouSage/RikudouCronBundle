<?php

namespace Rikudou\CronBundle\Cron;

interface DisableableCronJobInterface extends CronJobInterface
{
    /**
     * Whether the cron job is disabled or not
     *
     * @return bool
     */
    public function isEnabled(): bool;
}

<?php

namespace Rikudou\CronBundle\Helper;

trait DisabledCronJob
{

    /**
     * Disables the cron job that uses this trait
     */
    public function isEnabled(): bool
    {
        return false;
    }

}

<?php

namespace Rikudou\CronBundle\Helper;

trait DisabledCronJob
{

    /**
     * Disables the cron job that uses this trait
     * @return bool
     */
    public function isEnabled(): bool
    {
        return false;
    }

}

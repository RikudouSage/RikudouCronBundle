<?php

namespace Rikudou\CronBundle\Helper;

trait DisabledCronJob
{

    public function isEnabled(): bool
    {
        return false;
    }

}
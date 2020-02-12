<?php

namespace Rikudou\CronBundle\Event;

class CronEvents
{
    /**
     * Dispatched when there is an error in a running cron job
     *
     * @Event("\Rikudou\CronBundle\Event\CronJobErrorEvent")
     */
    public const ERROR = 'rikudou.cron.error';
}

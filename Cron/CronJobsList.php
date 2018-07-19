<?php

namespace Rikudou\CronBundle\Cron;

class CronJobsList
{

    /**
     * @var string[]
     */
    private $classes;

    /**
     * CronJobsList constructor.
     * @param string[] $classes
     */
    public function __construct(array $classes)
    {
        $this->classes = $classes;
    }

    /**
     * @return string[]
     */
    public function getClasses(): array
    {
        return $this->classes;
    }

}
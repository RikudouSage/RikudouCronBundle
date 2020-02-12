<?php


namespace Rikudou\CronBundle\Event;


use Rikudou\CronBundle\DTO\CronJobError;
use Symfony\Contracts\EventDispatcher\Event;

class CronJobErrorEvent extends Event
{
    /**
     * @var CronJobError[]
     */
    private $errors;

    /**
     * @param CronJobError[] $errors
     */
    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @return CronJobError[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}

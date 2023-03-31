<?php


namespace Rikudou\CronBundle\Event;


use Rikudou\CronBundle\DTO\CronJobError;
use Symfony\Contracts\EventDispatcher\Event;

final class CronJobErrorEvent extends Event
{
    /**
     * @param CronJobError[] $errors
     */
    public function __construct(
        private array $errors
    ) {
    }

    /**
     * @return CronJobError[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}

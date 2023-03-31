<?php


namespace Rikudou\CronBundle\DTO;


use DateTimeInterface;
use Throwable;

final class CronJobError
{
    public function __construct(
        private string $name,
        private Throwable $exception,
        private DateTimeInterface $dateTime,
    ) {
    }

    public function getException(): Throwable
    {
        return $this->exception;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDateTime(): DateTimeInterface
    {
        return $this->dateTime;
    }

    public function getError(): string
    {
        return $this->exception->getMessage();
    }
}

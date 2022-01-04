<?php


namespace Rikudou\CronBundle\DTO;


use DateTimeInterface;
use Throwable;

final class CronJobError
{
    /**
     * @var Throwable
     */
    private $exception;
    /**
     * @var string
     */
    private $name;
    /**
     * @var DateTimeInterface
     */
    private $dateTime;

    public function __construct(string $name, Throwable $exception, DateTimeInterface $dateTime)
    {
        $this->exception = $exception;
        $this->name = $name;
        $this->dateTime = $dateTime;
    }

    /**
     * @return Throwable
     */
    public function getException(): Throwable
    {
        return $this->exception;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return DateTimeInterface
     */
    public function getDateTime(): DateTimeInterface
    {
        return $this->dateTime;
    }

    public function getError(): string
    {
        return $this->exception->getMessage();
    }
}

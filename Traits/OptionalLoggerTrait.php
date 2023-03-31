<?php


namespace Rikudou\CronBundle\Traits;


use Psr\Log\LoggerInterface;

trait OptionalLoggerTrait
{
    private ?LoggerInterface $logger = null;

    public function setLogger(?LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}

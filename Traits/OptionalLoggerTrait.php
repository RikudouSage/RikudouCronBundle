<?php


namespace Rikudou\CronBundle\Traits;


use Psr\Log\LoggerInterface;

trait OptionalLoggerTrait
{
    /**
     * @var LoggerInterface|null
     */
    private $logger = null;

    public function setLogger(?LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}

<?php

namespace Rikudou\CronBundle\Cron;

use Cron\CronExpression;

class CronJobList
{
    /**
     * @var CronJobInterface[]
     */
    private $cronJobs = [];

    /**
     * @var array<string,array<string, string>>
     */
    private $commands = [];

    public function addCronJob(CronJobInterface $cronJob)
    {
        $key = get_class($cronJob);
        if ($cronJob instanceof NamedCronJobInterface) {
            $key = $cronJob->getName();
        }

        $this->cronJobs[$key] = $cronJob;
    }

    public function setCommands(array $commands)
    {
        $this->commands = $commands;
    }

    /**
     * @return CronJobInterface[]
     */
    public function getCronJobs(): array
    {
        return $this->cronJobs;
    }

    /**
     * @return array<string,array<string, string>>
     */
    public function getCommands(): array
    {
        return $this->commands;
    }

    public function findByName(string $name): ?CronJobInterface
    {
        return $this->cronJobs[$name] ?? null;
    }

    /**
     * @return CronJobInterface[]
     */
    public function getDueJobs(): array
    {
        return array_filter($this->cronJobs, function (CronJobInterface $cronJob) {
            if ($cronJob instanceof DisableableCronJobInterface && !$cronJob->isEnabled()) {
                return false;
            }
            $cronExpression = CronExpression::factory($cronJob->getCronExpression());
            return $cronExpression->isDue();
        });
    }

    /**
     * @return array<string,array<string,string>>
     */
    public function getDueCommands(): array
    {
        return array_filter($this->commands, function (array $commandData) {
            if (!$commandData['enabled']) {
                return false;
            }
            $cronExpression = CronExpression::factory($commandData['expression']);
            return $cronExpression->isDue();
        });
    }
}

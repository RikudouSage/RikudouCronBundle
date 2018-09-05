<?php

namespace Rikudou\CronBundle\DependencyInjection;

use Rikudou\CronBundle\Cron\CronJobsList;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CronJobsCompilerPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition(CronJobsList::class);
        $services = $container->findTaggedServiceIds("rikudou_cron_job.cronjob");
        foreach ($services as $service => $null) {
            $cronJobDefinition = $container->getDefinition($service);
            $cronJobDefinition->setPublic(true);
        }
        $definition->addArgument(array_keys($services));
    }
}
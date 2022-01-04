<?php

namespace Rikudou\CronBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class CronJobsCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $cronJobListService = $container->getDefinition('rikudou.cron.cron_job_list');

        $cronJobInfo = $container->findTaggedServiceIds('rikudou.cron.cronjob');
        foreach ($cronJobInfo as $serviceId => $tags) {
            $cronJobListService->addMethodCall('addCronJob', [new Reference($serviceId)]);
        }
    }
}

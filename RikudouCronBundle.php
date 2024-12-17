<?php

namespace Rikudou\CronBundle;

use Rikudou\CronBundle\Cron\CronJobInterface;
use Rikudou\CronBundle\DependencyInjection\Compiler\CronJobsCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class RikudouCronBundle extends Bundle
{

    public function build(ContainerBuilder $container): void
    {
        $container->registerForAutoconfiguration(CronJobInterface::class)->addTag("rikudou.cron.cronjob");
        $container->addCompilerPass(new CronJobsCompilerPass());
    }

}

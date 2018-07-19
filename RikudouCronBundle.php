<?php

namespace Rikudou\CronBundle;

use Rikudou\CronBundle\Cron\CronJobInterface;
use Rikudou\CronBundle\DependencyInjection\CronJobsCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class RikudouCronBundle extends Bundle
{

    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->registerForAutoconfiguration(CronJobInterface::class)->addTag("rikudou_cron_job.cronjob");
        $container->addCompilerPass(new CronJobsCompilerPass());
    }

}
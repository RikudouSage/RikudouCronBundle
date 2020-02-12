<?php

namespace Rikudou\CronBundle\DependencyInjection;

use Rikudou\CronBundle\Cron\CronJobList;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class RikudouCronExtension extends Extension
{

    /**
     * Loads a specific configuration.
     *
     * @param array $configs
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . "/../Resources/config"));
        $loader->load("services.yaml");

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $commands = [];
        foreach ($config["commands"] as $name => $command) {
            $commands[$name] = [
                'command' => $command['command'],
                'expression' => $command['cron_expression'],
                'enabled' => $command['enabled']
            ];
        }

        $definition = $container->getDefinition('rikudou.cron.cron_job_list');
        $definition->addMethodCall('setCommands', [$commands]);

        $container->setAlias('rikudou.cron.logger', $config['logger_service']);
    }
}

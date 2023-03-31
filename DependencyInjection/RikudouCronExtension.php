<?php

namespace Rikudou\CronBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class RikudouCronExtension extends Extension
{

    /**
     * Loads a specific configuration.
     *
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
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

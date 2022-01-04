<?php

namespace Rikudou\CronBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('rikudou_cron');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('logger_service')
                    ->info('The logger service that will be used')
                    ->defaultValue('logger')
                ->end()
                ->arrayNode("commands")
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode("command")
                                ->isRequired()
                                ->info('The command that will be executed')
                            ->end()
                            ->scalarNode("cron_expression")
                                ->isRequired()
                                ->info("A valid cron expression, e.g. '0 * * * *")
                            ->end()
                            ->booleanNode('enabled')
                                ->defaultTrue()
                                ->info('Whether the node is enabled or not')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

}

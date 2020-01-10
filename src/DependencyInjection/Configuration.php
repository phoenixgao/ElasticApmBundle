<?php

namespace SpaceSpell\ElasticApmBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('elastic_apm');

        $rootNode
            ->children()
                ->booleanNode('enabled')
                    ->defaultTrue()
                ->end()
                ->arrayNode('agent')
                    ->children()
                        ->scalarNode('appName')->isRequired()->end()
                        ->scalarNode('appVersion')->defaultValue('')->end()
                        ->scalarNode('serverUrl')->defaultValue('http://127.0.0.1:8200')->end()
                        ->scalarNode('secretToken')->defaultNull()->end()
                        ->integerNode('timeout')->defaultValue(5)->end()
                        ->arrayNode('env')
                            ->scalarPrototype()->end()
                        ->end()
                        ->arrayNode('cookies')
                            ->scalarPrototype()->end()
                        ->end()
                        ->arrayNode('httpClient')
                            ->scalarPrototype()->end()
                        ->end()
                        ->integerNode('backtraceLimit')->defaultValue(0)->end()
                    ->end()
                ->end()
                ->scalarNode('sharedContextProvider')->defaultNull()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

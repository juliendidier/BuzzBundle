<?php

namespace Buzz\Bundle\BuzzBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @inheritdoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root("buzz");

        $rootNode
            ->children()
                ->scalarNode('timeout')->cannotBeEmpty()->end()
                // @todo
                // ->scalarNode('debug')->defaultValue('%kernel.debug%')->end()
                ->arrayNode('clients')
                    ->useAttributeAsKey('client')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('timeout')->isRequired()->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('browsers')
                    ->useAttributeAsKey('browser')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('client')->isRequired()->end()
                            ->scalarNode('host')->isRequired()->end()
                            ->scalarNode('message_factory')->isRequired()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

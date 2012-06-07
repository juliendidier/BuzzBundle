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
                // @todo
                // ->scalarNode('debug')->defaultValue('%kernel.debug%')->end()
                ->arrayNode('browsers')
                    ->useAttributeAsKey('browser')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('client')->isRequired()->end()
                            ->scalarNode('message_factory')->isRequired()->end()
                            ->scalarNode('host')->isRequired()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

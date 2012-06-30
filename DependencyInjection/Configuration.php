<?php

namespace Buzz\Bundle\BuzzBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
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

        $this->addListenerSection($rootNode);
        $this->addBrowserSection($rootNode);

        return $treeBuilder;
    }

    private function addBrowserSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->booleanNode('profiler')->defaultValue('%kernel.debug%')->end()
                ->arrayNode('browsers')
                    ->useAttributeAsKey('browser')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('client')->end()
                            ->scalarNode('message_factory')->end()
                            ->scalarNode('host')->end()
                            ->arrayNode('listeners')
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addListenerSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('listeners')
                    ->useAttributeAsKey('key')
                    ->prototype('array')
                        ->beforeNormalization()
                            ->ifTrue(function($v){ return is_string($v); })
                            ->then(function($v){ return array('id' => $v); })
                        ->end()
                        ->children()
                            ->scalarNode('id')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}

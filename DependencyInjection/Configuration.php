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

        $rootNode
            ->children()
                // @todo
                // ->scalarNode('debug')->defaultValue('%kernel.debug%')->end()
            ->end()
        ;

        $this->addListenerSection($rootNode);
        $this->addBrowserSection($rootNode);

        return $treeBuilder;
    }

    private function addBrowserSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('browsers')
                    ->useAttributeAsKey('browser')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('client')->isRequired()->end()
                            ->scalarNode('message_factory')->isRequired()->end()
                            ->scalarNode('host')->isRequired()->end()
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
                            ->ifTrue(function($v){ return is_string($v) && 0 === strpos($v, '@'); })
                            ->then(function($v){ return array('id' => substr($v, 1)); })
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

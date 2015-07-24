<?php

namespace Buzz\Bundle\BuzzBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    private $debug;

    public function __construct($debug)
    {
        $this->debug = (bool) $debug;
    }

    /**
     * @inheritdoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('buzz');

        $rootNode
            ->children()
                ->booleanNode('profiler')->defaultValue($this->debug)->end()
                ->booleanNode('throw_exception')->defaultValue(true)->end()
                ->end()
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
                        ->append($this->getClientConfiguration())
                        ->children()
                            ->scalarNode('message_factory')->defaultValue(null)->end()
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

    private function getClientConfiguration()
    {
        $node = new ArrayNodeDefinition('client');

        return $node
            ->beforeNormalization()
                ->ifTrue(function($v){ return is_string($v); })
                ->then(function($v){ return array('name' => $v); })
            ->end()
            ->children()
                ->scalarNode('name')->end()
                ->scalarNode('timeout')->defaultNull()->end()
                ->scalarNode('proxy')->defaultNull()->end()
                ->scalarNode('max_redirects')->defaultNull()->end()
                ->scalarNode('verify_peer')->defaultNull()->end()
                ->scalarNode('verify_host')->defaultNull()->end()
                ->scalarNode('ignore_errors')->defaultNull()->end()
            ->end()
        ;
    }
}

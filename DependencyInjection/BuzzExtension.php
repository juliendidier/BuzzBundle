<?php

namespace Buzz\Bundle\BuzzBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class BuzzExtension extends Extension
{
    /**
     * @inheritdoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('buzz.xml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->loadListenersSection($config['listeners'], $container);
        $this->loadBrowsersSection($config['browsers'], $container);

        if ($config['profiler']) {
            $this->loadProfiler(array_keys($config['browsers']), $container);
        }

        $container->setParameter('buzz', $config);

        return $config;
    }

    private function loadListenersSection(array $config, ContainerBuilder $container)
    {
        $listeners = array();
        foreach ($config as $key => $listener) {
            $listeners[$key] = $listener['id'];
        }
    }

    private function loadBrowsersSection(array $config, ContainerBuilder $container)
    {
        foreach ($config as $name => $browserConfig) {
            $this->createBrowser($name, $browserConfig, $container);
         }
    }

    private function createBrowser($name, array $config, ContainerBuilder $container)
    {
        $browser = 'buzz.browser.'.$name;

        $container->register($browser, 'Buzz\Browser')
            ->setArguments(array(null, null))
            ->addTag('buzz.browser', array('alias'=> $name))
        ;

        $browser = 'buzz.browser.'.$name;
        $browser = $container->getDefinition($browser);
        if (isset($config['client']) && !empty($config['client'])) {
            $browser
                ->replaceArgument(0, new Reference('buzz.client.'.$config['client']))
                ->replaceArgument(1, null)
            ;
        }

        if (!empty($config['host'])) {
            $listener = 'buzz.listener.host_'.$name;

            $container
                ->register($listener, 'Buzz\Bundle\BuzzBundle\Buzz\Listener\HostListener')
                ->addArgument($config['host'])
            ;

            $browser->addMethodCall('addListener', array(new Reference($listener)));
        }
    }

    private function loadProfiler(array $browserNames, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('datacollector.xml');

        foreach($browserNames as $name) {
            $container->getDefinition('buzz.browser.'.$name)
                ->addMethodCall('addListener', array(new Reference('buzz.listener.history')))
            ;

        }
    }
}

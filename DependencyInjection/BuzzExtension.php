<?php

namespace Buzz\Bundle\BuzzBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
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

        if ($config['throw_exception']) {
            $config = $this->configureExceptionListener($config, $container);
        }

        $listeners = $this->loadListenersSection($config['listeners'], $container);
        $this->loadBrowsersSection($config['browsers'], $listeners, $container);

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
            $listeners[$key] = new Reference($listener['id']);
        }

        return $listeners;
    }

    private function loadBrowsersSection(array $config, array $listeners, ContainerBuilder $container)
    {
        foreach ($config as $name => $browserConfig) {
            $browser = $this->createBrowser($name, $browserConfig, $container);
            $this->configureBrowser($browser, $browserConfig, $listeners);
         }
    }

    private function configureBrowser(Definition $browser, array $browserConfig, array $listeners)
    {
        foreach ($browserConfig['listeners'] as $listener) {
            $browser->addMethodCall('addListener', array($listeners[$listener]));
        }
    }

    private function createBrowser($name, array $config, ContainerBuilder $container)
    {
        $browser = 'buzz.browser.'.$name;

        $definition = $container->register($browser, 'Buzz\Browser')
            ->setArguments(array(null, null))
        ;

        if (null !== $config['message_factory']) {
            $factory = 'buzz.message_factory.'.$name;
            $container->register($factory, $config['message_factory']);

            $definition->replaceArgument(1, new Reference($factory));
        }

        $container->getDefinition('buzz.browser_manager')
            ->addMethodCall('set', array($name, new Reference($browser)))
        ;

        $browser = $container->getDefinition($browser);

        $this->configureClientBrowser($name, $browser, $config, $container);

        if (!empty($config['host'])) {
            $listener = 'buzz.listener.host_'.$name;

            $container
                ->register($listener, 'Buzz\Bundle\BuzzBundle\Buzz\Listener\HostListener')
                ->addArgument($config['host'])
            ;

            $browser->addMethodCall('addListener', array(new Reference($listener)));
        }

        return $browser;
    }

    private function configureClientBrowser($name, Definition $browser, array $config, ContainerBuilder $container)
    {
        $baseDefinition = new DefinitionDecorator('buzz.client.'.$config['client']['name']);
        $container->setDefinition('buzz.client.'.$name,$baseDefinition);
        $definition = $container->getDefinition('buzz.client.'.$name);

        $timeout = $config['client']['timeout'];
        if (null !== $timeout) {
            $definition->addMethodCall('setTimeout', array($timeout));
        }

        $proxy = $config['client']['proxy'];
        if (null !== $proxy) {
            $definition->addMethodCall('setProxy', array($proxy));
        }

        $browser->replaceArgument(0, new Reference('buzz.client.'.$name));
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

    private function configureExceptionListener(array $config, ContainerBuilder $container)
    {
        $listenerName = 'exception_listener';
        $listener = 'buzz.listener.'.$listenerName;

        $container->register($listener, 'Buzz\Bundle\BuzzBundle\Buzz\Listener\ExceptionListener');

        $config['listeners'][$listenerName] = array('id' => $listener);

        foreach($config['browsers'] as $name => $browserConfig) {
            array_unshift($config['browsers'][$name]['listeners'], $listenerName);
        }

        return $config;
    }
}

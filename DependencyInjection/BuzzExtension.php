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
        $configs = $this->processConfiguration($configuration, $configs);

        $configs = $this->loadListeners($configs, $container);
        $configs = $this->loadBrowsers($configs, $container);

        $container->setParameter('buzz', $configs);

        return $configs;
    }

    private function loadListeners(array $configs, ContainerBuilder $container)
    {
        $listeners = array();
        foreach ($configs['listeners'] as $key => $listener) {
            $listeners[$key] = $listener['id'];
        }

        return array_replace($configs, array('listeners' => $listeners));
    }

    private function loadBrowsers(array $configs, ContainerBuilder $container)
    {
        foreach ($configs['browsers'] as $id => $config) {
            $this->createBrowser($id, $configs, $container);
            $configs = $this->configureBrowser($id, $configs, $container);
         }

        return $configs;
    }

    private function createBrowser($id, array $configs, ContainerBuilder $container)
    {
        $browser = 'buzz.browser.'.$id;
        $config = $configs['browsers'][$id];

        $container->register($browser, 'Buzz\Browser')
            ->setArguments(array(null, null))
        ;

        return $configs;
    }

    private function configureBrowser($id, array $configs, ContainerBuilder $container)
    {
        $config = $configs['browsers'][$id];
        $browser = 'buzz.browser.'.$id;
        $browser = $container->getDefinition($browser)
            ->replaceArgument(0, new Reference('buzz.client.'.$config['client']))
            ->replaceArgument(1, null)
        ;

        if (!empty($config['host'])) {
            $listener = 'buzz.listener.host_'.$id;

            $container
                ->register($listener, 'Buzz\Bundle\BuzzBundle\Buzz\Listener\HostListener')
                ->addArgument($config['host'])
            ;

            $configs['listeners']['host_'.$id] = $listener;
            $configs['browsers'][$id]['listeners'][] = 'host_'.$id;
        }

        return $configs;
    }
}

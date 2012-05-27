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
        $loader->load('services.xml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $browsers = array();
        foreach ($config['browsers'] as $id => $browser) {
            $browsers[$id] = $this->createBrowser($id, $browser, $container);
        }
   }

   private function createBrowser($id, array $config, ContainerBuilder $container)
   {
        $provider = 'buzz.browser.'.$id;
        $definition = new DefinitionDecorator('buzz.browser');

        $container
            ->setDefinition($provider, $definition)
            ->replaceArgument(0, new Reference($config['client']))
            ->replaceArgument(1, $config['url'])
            ->addTag('buzz.browser', array('alias' => $id))
        ;

        return $provider;
   }
}

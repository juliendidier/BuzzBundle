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

        foreach ($config['browsers'] as $id => $browser) {
            $this->createBrowser($id, $browser, $container);
        }
   }

   private function createBrowser($id, array $config, ContainerBuilder $container)
   {
        $browser = 'buzz.browser.'.$id;

        if ($container->hasDefinition($browser)) {
            return $container->getDefinition($browser);
        }

        $definition = new DefinitionDecorator('buzz.browser');

        $container
            ->setDefinition($browser, $definition)
            ->replaceArgument(0, $config['host'])
            ->replaceArgument(1, new Reference('buzz.client.'.$config['client']))
            ->replaceArgument(2, null)
            ->addTag('buzz.browser', array('alias' => $id))
        ;

        return $browser;
   }
}

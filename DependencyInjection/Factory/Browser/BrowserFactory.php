<?php

namespace Buzz\Bundle\BuzzBundle\DependencyInjection\Factory\Browser;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class BrowserFactory
{
    function create(ContainerBuilder $container, $id, $config)
    {
        $provider = 'buzz.browser.'.$id;
        $this->container
            ->setDefinition($provider, new DefinitionDecorator('buzz.browser'))
            ->replaceArgument(0, new Reference($config['client']))
            ->replaceArgument(1, $config['url'])
        ;

        return $provider;
    }
}

<?php

namespace Buzz\Bundle\BuzzBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Adds all services with the tags "buzz.browser" as arguments of the
 * "buzz.browser_manager" service
 *
 * @author Julien DIDIER <julien@jdidier.net>
 */
class BrowserPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('buzz.browser_manager')) {
            return;
        }

        $bm = $container->getDefinition('buzz.browser_manager');
        $config = $container->getParameter('buzz');

        foreach ($container->findTaggedServiceIds('buzz.browser') as $serviceId => $tag) {
            $name = isset($tag[0]['alias'])
                ? $tag[0]['alias']
                : $serviceId;


            $baseDefinition = $container->getDefinition('buzz.browser.'.$name);
            $definition = $container->getDefinition($serviceId);
            $clientId = 'buzz.client.'.$config['browsers'][$name]['client'];

            $baseDefinition->replaceArgument(0, new Reference($clientId));

            $arguments = $baseDefinition->getArguments();
            foreach ($arguments as $index => $argument) {
                $definition->replaceArgument($index, $argument);
            }

            $calls = $baseDefinition->getMethodCalls();
            $definition->setMethodCalls($calls);

            $browserConfig = $config['browsers'][$name];

            foreach ($browserConfig['listeners'] as $listenerName) {
                $listener = $config['listeners'][$listenerName];
                $definition->addMethodCall('addListener', array(new Reference($listener['id'])));
            }

            $bm->addMethodCall('set', array($name, new Reference($serviceId)));
        }
    }
}

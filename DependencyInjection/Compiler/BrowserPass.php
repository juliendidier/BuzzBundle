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
        $configs = $container->getParameter('buzz');

        foreach ($container->findTaggedServiceIds('buzz.browser') as $serviceId => $tag) {
            $alias = isset($tag[0]['alias'])
                ? $tag[0]['alias']
                : $serviceId;
            if ($container->hasDefinition('buzz.browser.'.$alias)) {
                $browser = $container->getDefinition('buzz.browser.'.$alias);
                $container->getDefinition($serviceId)
                    ->replaceArgument(0, $browser->getArgument(0))
                    ->replaceArgument(1, $browser->getArgument(1))
                ;

                foreach ($configs['browsers'][$alias]['listeners'] as $listener) {
                    $listener = new Reference($configs['listeners'][$listener]);
                    $container->getDefinition($serviceId)
                        ->addMethodCall('addListener', array($listener));
                }
            }

            $bm->addMethodCall('set', array($alias, new Reference($serviceId)));
        }
    }
}

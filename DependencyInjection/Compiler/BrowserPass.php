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

        foreach ($container->findTaggedServiceIds('buzz.browser') as $id => $attributes) {
            foreach ($attributes as $attr) {
                if (isset($attr['alias'])) {
                    $bm->addMethodCall('set', array($attr['alias'], new Reference($id)));
                }
            }
        }
    }
}

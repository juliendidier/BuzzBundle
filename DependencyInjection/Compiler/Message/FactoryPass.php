<?php

namespace Buzz\Bundle\BuzzBundle\DependencyInjection\Compiler\Message;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Adds all services with the tags "buzz.message_factory" as arguments of the
 * "buzz.message.factory_manager" service
 *
 * @author Kris Wallsmith <kris@symfony.com>
 */
class FactoryPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('buzz.message.factory_manager')) {
            return;
        }

        $bmf = $container->getDefinition('buzz.message.factory_manager');

        foreach ($container->findTaggedServiceIds('buzz.message_factory') as $id => $attributes) {
            foreach ($attributes as $attr) {
                if (isset($attr['alias'])) {
                    $bmf->addMethodCall('set', array($attr['alias'], new Reference($id)));
                }
            }
        }
    }
}

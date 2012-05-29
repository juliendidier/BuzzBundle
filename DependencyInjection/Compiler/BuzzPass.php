<?php

namespace Buzz\Bundle\BuzzBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Adds all services with the tags "buzz.browser" as arguments of the
 * "buzz" service
 *
 * @author Bernhard Schussek <bernhard.schussek@symfony-project.com>
 */
class BuzzPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('buzz')) {
            return;
        }

        // Builds an array with service IDs as keys and tag aliases as values
        $browsers = array();
        foreach ($container->findTaggedServiceIds('buzz.browser') as $serviceId => $tag) {
            $alias = isset($tag[0]['alias'])
                ? $tag[0]['alias']
                : $serviceId;
            // Flip, because we want tag aliases (= type identifiers) as keys
            $browsers[$alias] = $serviceId;
        }

        $container->getDefinition('buzz')->replaceArgument(1, $browsers);
    }
}

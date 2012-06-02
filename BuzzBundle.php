<?php

namespace Buzz\Bundle\BuzzBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class BuzzBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new DependencyInjection\Compiler\Message\FactoryPass());
        $container->addCompilerPass(new DependencyInjection\Compiler\BrowserPass());
    }
}

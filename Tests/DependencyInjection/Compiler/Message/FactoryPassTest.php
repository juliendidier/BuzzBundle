<?php

namespace Buzz\Bundle\BuzzBundle\Tests\DependencyInjection\Factory\Message;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

use Buzz\Bundle\BuzzBundle\DependencyInjection\Compiler\Message\FactoryPass;

class FactoryPassTest extends \PHPUnit_Framework_TestCase
{
    public function testProcess()
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $factoryPass = new FactoryPass();
        $factory = $this->getMock('Buzz\Bundle\BuzzBundle\Buzz\Message\FactoryManager');

        $return = $factoryPass->process($container);
        $this->assertEquals(null, $return, 'No buzz.message.factory_manager registered');

        $container = $this->getContainer();
        $def = $container
            ->register('foo')
            ->setClass('Buzz\Message\Factory\Factory')
            ->addTag('buzz.message_factory', array('alias' => 'bar'))
        ;

        $factoryPass->process($container);

        $factory = $container->get('buzz.message.factory_manager');
        $this->assertTrue($factory->has('bar'));
        $this->assertInstanceOf('Buzz\Message\Factory\Factory', $factory->get('bar'));
    }

    protected function getContainer()
    {
        $container = new ContainerBuilder();
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../../../../Resources/config'));
        $loader->load('buzz.xml');

        return $container;
    }
}

<?php

namespace Buzz\Bundle\BuzzBundle\Tests\DependencyInjection;

use Buzz\Bundle\BuzzBundle\DependencyInjection\BuzzExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

class BuzzExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $container = new ContainerBuilder();
        $extension = new BuzzExtension();
        $configs = $extension->load($this->getConfig(), $container);

        $this->assertTrue($container->has('buzz.browser.foo'));
        $this->assertTrue($container->has('buzz.browser.bar'));

        $this->assertTrue($container->has('buzz.browser.foo'));
        $browser = $container->getDefinition('buzz.browser.foo');

        $client = new Reference('buzz.client.curl');
        $this->assertEquals($client, $browser->getArgument(0));
        $this->assertNull($browser->getArgument(1));
    }

    public function testLoadWithDefinedBrowser()
    {
        $container = new ContainerBuilder();
        $extension = new BuzzExtension();
        $definition = new DefinitionDecorator('buzz.browser');
        $container
            ->setDefinition('buzz.browser.bar', $definition)
        ;
        $extension->load($this->getConfig(), $container);

        $this->assertTrue($container->has('buzz.browser.bar'));
    }

    private function getConfig()
    {
        return array(
            array('browsers' => array(
                'foo' => array(
                    'client' => 'curl',
                    'message_factory' => 'foo',
                    'host' => 'my://foohost',
                ),
                'bar' => array(
                    'client' => 'curl',
                    'message_factory' => 'bar',
                    'host' => 'my://barhost',
                )
            )),
        );
    }
}

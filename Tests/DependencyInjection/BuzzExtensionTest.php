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
        $extension->load($this->getConfig(), $container);

        $this->assertTrue($container->has('buzz.browser.foo'));
        $this->assertTrue($container->has('buzz.browser.bar'));

        $browser = $container->getDefinition('buzz.browser.foo');
        $this->assertEquals('buzz.browser', $browser->getParent());
        $this->assertEquals('my://foohost', $browser->getArgument(0));
        $client = new Reference('buzz.client.curl');
        $this->assertEquals($client, $browser->getArgument(1));
        $this->assertNull($browser->getArgument(2));

    }

    public function testLoadWithDefinedBrowser()
    {
        $container = new ContainerBuilder();
        $extension = new BuzzExtension();
        $definition = new DefinitionDecorator('buzz.browser');
        $container
            ->setDefinition('buzz.browser.bar', $definition)
            ->replaceArgument(0, 'my://otherfoohost')
        ;
        $extension->load($this->getConfig(), $container);

        $browser = $container->getDefinition('buzz.browser.bar');
        $this->assertTrue($container->has('buzz.browser.bar'));
        $this->assertEquals('my://otherfoohost', $browser->getArgument(0));
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

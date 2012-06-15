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
        $this->assertEquals(array('host' => 'foo.bar', 'host_foo' => 'buzz.listener.host_foo'), $configs['listeners']);

        $this->assertTrue($container->hasDefinition('buzz.browser.foo'));
        $browser = $container->getDefinition('buzz.browser.foo');
        $client = new Reference('buzz.client.curl');
        $this->assertEquals($client, $browser->getArgument(0));
        $this->assertNull($browser->getArgument(1));

        $this->assertTrue($container->hasDefinition('buzz.listener.host_foo'));
        $listener = $container->getDefinition('buzz.listener.host_foo');
        $this->assertEquals('my://foo', $listener->getArgument(0));
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

        $this->assertTrue($container->hasDefinition('buzz.browser.bar'));
    }

    private function getBadConfig()
    {
        return array(
            array(
                'listeners' => array(
                    'host' => 'host'
                )
            )
        );
    }

    private function getConfig()
    {
        return array(
            array(
                'listeners' => array(
                    'host' => 'foo.bar'
                ),
                'browsers' =>
                    array(
                    'foo' => array(
                        'client' => 'curl',
                        'message_factory' => 'foo',
                        'host' => 'my://foo',
                        'listeners' => array(
                            'host',
                        )
                    )
                )
            )
        );
    }
}

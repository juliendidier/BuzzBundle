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
        $this->assertEquals(array('host' => array('id' => 'foo.bar')), $configs['listeners']);

        $this->assertTrue($container->hasDefinition('buzz.browser.foo'));
        $browser = $container->getDefinition('buzz.browser.foo');
        $client = new Reference('buzz.client.curl');
        $this->assertEquals($client, $browser->getArgument(0));
        $this->assertNull($browser->getArgument(1));

        $this->assertTrue($container->hasDefinition('buzz.listener.host_foo'));
        $listener = $container->getDefinition('buzz.listener.host_foo');
        $this->assertEquals('my://foo', $listener->getArgument(0));
    }

    public function testLoadClients()
    {
        $container = new ContainerBuilder();
        $extension = new BuzzExtension();
        $configs = $extension->load(array(), $container);

        $array = function($name) { return array(array('alias' => 'curl')); };

        $this->assertTrue($container->hasDefinition('buzz.client.curl'));
        $this->assertEquals($array('curl'), $container->getDefinition('buzz.client.curl')->getTag('buzz.client'));
        $this->assertTrue($container->hasDefinition('buzz.client.multi_curl'));
        $this->assertEquals($array('multi_curl'), $container->getDefinition('buzz.client.curl')->getTag('buzz.client'));
        $this->assertTrue($container->hasDefinition('buzz.client.file_get_contents'));
        $this->assertEquals($array('file_get_contents'), $container->getDefinition('buzz.client.curl')->getTag('buzz.client'));
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

    public function testProfilerConfig()
    {
        $container = new ContainerBuilder();
        $extension = new BuzzExtension();

        $configs = $extension->load($this->getProfilerConfig(), $container);

        $this->assertTrue($container->hasDefinition('buzz.data_collector'));
        $this->assertTrue($container->hasDefinition('buzz.listener.history'));
        $this->assertTrue($container->hasDefinition('buzz.listener.history_journal'));
        $collector = $container->getDefinition('buzz.data_collector');
        $history = $container->getDefinition('buzz.listener.history');
        $joural = $container->getDefinition('buzz.listener.history_journal');

        $this->assertEquals(new Reference('buzz.listener.history'), $collector->getArgument(0));
        $this->assertEquals(new Reference('buzz.listener.history_journal'), $history->getArgument(0));

        $browser = $container->getDefinition('buzz.browser.foo');
        $this->assertTrue($browser->hasMethodCall('addListener'));
        $calls = $browser->getMethodCalls();
        $this->assertEquals(new Reference('buzz.listener.history'), $calls[0][1][0]);
    }

    private function getConfig()
    {
        return array(
            array(
                'listeners' => array(
                    'host' => 'foo.bar'
                ),
                'browsers' => array(
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

    private function getProfilerConfig()
    {
        return array(
            array(
                'profiler' =>  true,
                'browsers' => array(
                    'foo' => array()
                )
            )
        );
    }
}

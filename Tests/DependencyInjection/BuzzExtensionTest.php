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

        $this->assertSame('%kernel.debug%', $configs['profiler']);
        $this->assertSame(true, $configs['throw_exception']);
        $this->assertSame(array('id' => 'foo.bar'), $configs['listeners']['foo_bar']);

        $this->assertTrue($container->hasDefinition('buzz.browser.foo'));
        $browser = $container->getDefinition('buzz.browser.foo');
        $this->assertNull($browser->getArgument(1));
        $calls = $browser->getMethodCalls();
        $this->assertCount(4, $calls);

        $expected = array('addListener', array(new Reference('buzz.listener.host_foo')));
        $this->assertEquals($expected, $calls[0]);
        $expected = array('addListener', array(new Reference('buzz.listener.exception_listener')));
        $this->assertEquals($expected, $calls[1]);
        $expected = array('addListener', array(new Reference('foo.bar')));
        $this->assertEquals($expected, $calls[2]);
        $expected = array('addListener', array(new Reference('buzz.listener.history')));
        $this->assertEquals($expected, $calls[3]);

        $calls = $container->getDefinition('buzz.browser_manager')->getMethodCalls();
        $this->assertCount(1, $calls);
        $expected = array('set', array('foo', new Reference('buzz.browser.foo')));
        $this->assertEquals($expected, $calls[0]);

        $client = new Reference('buzz.client.curl');
        $this->assertEquals($client, $browser->getArgument(0));

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
                    'foo_bar' => 'foo.bar'
                ),
                'browsers' => array(
                    'foo' => array(
                        'client' => 'curl',
                        'message_factory' => 'foo',
                        'host' => 'my://foo',
                        'listeners' => array(
                            'foo_bar',
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
                'throw_exception' => false,
                'profiler' =>  true,
                'browsers' => array(
                    'foo' => array()
                )
            )
        );
    }
}

<?php

namespace Buzz\Bundle\BuzzBundle\Tests\DependencyInjection;

use Buzz\Bundle\BuzzBundle\DependencyInjection\BuzzExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

class BuzzExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $container;
    private $configs;

    public function __construct()
    {
        $extension = new BuzzExtension();

        $this->container = new ContainerBuilder();
        $this->configs = $extension->load($this->getConfig(), $this->container);
    }

    public function testLoad()
    {
        $this->assertSame('%kernel.debug%', $this->configs['profiler']);
        $this->assertSame(true, $this->configs['throw_exception']);
    }

    public function testLoadBrowser()
    {
        $this->assertTrue($this->container->hasDefinition('buzz.browser.foo'));
        $this->assertTrue($this->container->hasDefinition('buzz.message_factory.foo'));

        $browser = $this->container->getDefinition('buzz.browser.foo');
        $this->assertEquals(new Reference('buzz.message_factory.foo'), $browser->getArgument(1));

        $factory = $this->container->getDefinition('buzz.message_factory.foo');
        $this->assertSame('Buzz\Message\Factory\Factory', $factory->getClass());

    }

    public function testLoadClient()
    {
        $browser = $this->container->getDefinition('buzz.browser.foo');

        $this->assertTrue($this->container->hasDefinition('buzz.client.foo'));
        $client = $this->container->getDefinition('buzz.client.foo');
        $this->assertTrue($client instanceof DefinitionDecorator);
        $curlClient = $this->container->get($client->getParent());
        $this->assertEquals('Buzz\Client\Curl', get_class($curlClient));

        $calls = $client->getMethodCalls();
        $this->assertCount(2, $calls);
        $expected = array('setTimeout', array(123));
        $this->assertEquals($expected, $calls[0]);
        $expected = array('setProxy', array('http://127.0.0.1'));
        $this->assertEquals($expected, $calls[1]);

        $client = new Reference('buzz.client.foo');
        $this->assertEquals($client, $browser->getArgument(0));
    }

    public function testLoadListener()
    {
        $this->assertSame(array('id' => 'foo.bar'), $this->configs['listeners']['foo_bar']);

        $browser = $this->container->getDefinition('buzz.browser.foo');
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

        $this->assertTrue($this->container->hasDefinition('buzz.listener.host_foo'));
        $listener = $this->container->getDefinition('buzz.listener.host_foo');
        $this->assertEquals('my://foo', $listener->getArgument(0));
    }

    public function testBrowserManagerCall()
    {
        $calls = $this->container->getDefinition('buzz.browser_manager')->getMethodCalls();
        $this->assertCount(1, $calls);
        $expected = array('set', array('foo', new Reference('buzz.browser.foo')));
        $this->assertEquals($expected, $calls[0]);
    }

    public function testProfilerConfig()
    {
        $this->container = new ContainerBuilder();
        $extension = new BuzzExtension();

        $this->configs = $extension->load($this->getProfilerConfig(), $this->container);

        $this->assertTrue($this->container->hasDefinition('buzz.data_collector'));
        $this->assertTrue($this->container->hasDefinition('buzz.listener.history'));
        $this->assertTrue($this->container->hasDefinition('buzz.listener.history_journal'));
        $collector = $this->container->getDefinition('buzz.data_collector');
        $history = $this->container->getDefinition('buzz.listener.history');
        $joural = $this->container->getDefinition('buzz.listener.history_journal');

        $this->assertEquals(new Reference('buzz.listener.history'), $collector->getArgument(0));
        $this->assertEquals(new Reference('buzz.listener.history_journal'), $history->getArgument(0));

        $browser = $this->container->getDefinition('buzz.browser.foo');
        $this->assertTrue($browser->hasMethodCall('addListener'));
        $calls = $browser->getMethodCalls();
        $this->assertEquals(new Reference('buzz.listener.history'), $calls[0][1][0]);
    }

    public function testBcBreakClient()
    {
        $this->container = new ContainerBuilder();
        $extension = new BuzzExtension();

        $array = array(array('browsers' => array('foo' => array('client' => 'curl'))));
        $this->configs = $extension->load($array, $this->container);

        $this->assertSame(array('name' => 'curl', 'timeout' => null, 'proxy' => null), $this->configs['browsers']['foo']['client']);
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
                        'client' => array('name' => 'curl', 'timeout' => 123, 'proxy' => 'http://127.0.0.1'),
                        'message_factory' => 'Buzz\\Message\\Factory\\Factory',
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
                    'foo' => array('client' => 'curl')
                )
            )
        );
    }
}

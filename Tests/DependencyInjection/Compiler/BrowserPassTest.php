<?php

namespace Buzz\Bundle\BuzzBundle\Tests\DependencyInjection\Factory\Message;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

use Buzz\Bundle\BuzzBundle\DependencyInjection\BuzzExtension;
use Buzz\Bundle\BuzzBundle\DependencyInjection\Compiler\BrowserPass;

class BrowserPassTest extends \PHPUnit_Framework_TestCase
{
    public function testProcess()
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $browserPass = new BrowserPass();

        $return = $browserPass->process($container);
        $this->assertEquals(null, $return, 'No buzz.browser_manager registered');

        $container = $this->getContainer();
        $def = $container
            ->register('buzz.browser.my_foo')
            ->setClass('Buzz\Browser')
            ->setArguments(array(null, null))
            ->addTag('buzz.browser', array('alias' => 'foo'))
        ;

        $browserPass->process($container);

        $this->assertTrue($container->get('buzz.browser_manager')->has('foo'));
        $browser = $container->get('buzz.browser_manager')->get('foo');
        $this->assertEquals($browser, $container->get('buzz.browser_manager')->get('foo'));
        $this->assertEquals($browser->getListener(), $container->get('buzz.listener.host_foo'));

        $listener = $container->getDefinition('buzz.listener.host_foo');
        $this->assertEquals('my://foo', $listener->getArgument(0));
    }

    protected function getContainer()
    {
        $container = new ContainerBuilder();
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../../../Resources/config'));
        $loader->load('buzz.xml');
        $extension = new BuzzExtension();
        $extension->load($this->getConfig(), $container);

        return $container;
    }

    private function getConfig()
    {
        return array(
            array('browsers' => array(
                'foo' => array(
                    'client' => 'curl',
                    'message_factory' => 'foo',
                    'host' => 'my://foo',
                ),
            )),
        );
    }
}

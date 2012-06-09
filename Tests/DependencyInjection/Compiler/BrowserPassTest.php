<?php

namespace Buzz\Bundle\BuzzBundle\Tests\DependencyInjection\Factory\Message;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

use Buzz\Bundle\BuzzBundle\DependencyInjection\Compiler\BrowserPass;

class BrowserPassTest extends \PHPUnit_Framework_TestCase
{
    public function testProcess()
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $browserPass = new BrowserPass();
        $browser = $this->getMock('Buzz\Browser');

        $return = $browserPass->process($container);
        $this->assertEquals(null, $return, 'No buzz.browser_manager registered');

        $container = $this->getContainer();
        $def = $container
            ->register('foo')
            ->setClass('Buzz\Browser')
            ->addTag('buzz.browser', array('alias' => 'bar'))
        ;

        $browserPass->process($container);

        $this->assertTrue($container->get('buzz.browser_manager')->has('bar'));
    }

    protected function getContainer()
    {
        $container = new ContainerBuilder();
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../../../Resources/config'));
        $loader->load('buzz.xml');

        return $container;
    }
}

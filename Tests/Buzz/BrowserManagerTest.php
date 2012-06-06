<?php

namespace Buzz\Bundle\BuzzBundle\Tests\Buzz;

use Buzz\Client\ClientInterface;
use Buzz\Message\RequestInterface;
use Buzz\Message\MessageInterface;
use Buzz\Message\Factory\Factory;

use Buzz\Bundle\BuzzBundle\Buzz\Browser;
use Buzz\Bundle\BuzzBundle\Buzz\BrowserManager;
use Buzz\Bundle\BuzzBundle\Buzz\Message\FactoryManager;

class BrowserManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSetHas()
    {
        $browserManager = new BrowserManager(new FactoryManagerMock());
        $foo = new Browser(new ClientMock());
        $bar = new Browser(new ClientMock());

        $browserManager->set('foo', $foo);
        $this->assertEquals(true, $browserManager->has('foo'));
        $this->assertEquals($foo, $browserManager->get('foo'));
        $this->assertEquals(false, $browserManager->has('bar'));

        $browserManager->set('foo', $bar);
        $this->assertEquals($bar, $browserManager->get('foo'));
    }

    /**
     * @dataProvider provideMethods
     */
    public function testBasicMethodsFail($method)
    {
        $browserManager = new BrowserManager(new FactoryManagerMock());
        $browser = new Browser(new ClientMock());

        try {
            $browserManager->$method($browser);
            $this->fail();
        } catch (\Exception $e) {
            $this->assertEquals('$name must be a string', $e->getMessage());
        }

        try {
            $browserManager->get('test');
            $this->fail();
        } catch (\Exception $e) {
            $this->assertEquals('Buzz browser with name "test" not found.', $e->getMessage());
        }

    }

    public function provideMethods()
    {
        return array(
            array('get'),
            array('has'),
        );
    }

}

class FactoryManagerMock extends FactoryManager
{
    public function has($name)
    {
        return true;
    }

    public function get($name)
    {
        return new Factory();
    }
}

class ClientMock implements ClientInterface
{
    public function send(RequestInterface $request, MessageInterface $response)
    {
        return;
    }
}

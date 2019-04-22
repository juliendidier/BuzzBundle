<?php

namespace Buzz\Bundle\BuzzBundle\Tests\Buzz;

use Buzz\Browser;
use Buzz\Client\ClientInterface;
use Buzz\Message\RequestInterface;
use Buzz\Message\MessageInterface;
use Buzz\Bundle\BuzzBundle\Buzz\BrowserManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BrowserManagerTest extends TestCase
{
    public function testGetSetHas()
    {
        $browserManager = new BrowserManager();
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
        $browserManager = new BrowserManager();
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

    /**
     * @throws \ReflectionException
     */
    public function testGetIterator()
    {
        $bm = new BrowserManager();
        /** @var Browser|MockObject $browserMock */
        $browserMock = $this->createMock(Browser::class);
        $bm->set('foo', $browserMock);
        $bm->set('bar', $browserMock);

        $this->assertTrue($bm->getIterator() instanceof \ArrayIterator);
        $this->assertEquals(2, count($bm));
    }
}

class ClientMock implements ClientInterface
{
    public function send(RequestInterface $request, MessageInterface $response)
    {
        return;
    }
}

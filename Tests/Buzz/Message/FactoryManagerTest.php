<?php

namespace Buzz\Bundle\BuzzBundle\Tests\Buzz\Message;

use Buzz\Client\ClientInterface;
use Buzz\Message\RequestInterface;
use Buzz\Message\MessageInterface;
use Buzz\Message\Factory\Factory;

use Buzz\Bundle\BuzzBundle\Buzz\Message\FactoryManager;

class FactoryManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSetHas()
    {
        $factoryManager = new FactoryManager();

        $foo = $this->getMock('Buzz\Message\Factory\FactoryInterface');
        $bar = $this->getMock('Buzz\Message\Factory\FactoryInterface');

        $factoryManager->set('foo', $foo);
        $this->assertEquals(true, $factoryManager->has('foo'));
        $this->assertEquals($foo, $factoryManager->get('foo'));
        $this->assertEquals(false, $factoryManager->has('bar'));

        $factoryManager->set('foo', $bar);
        $this->assertEquals($bar, $factoryManager->get('foo'));
    }

    /**
     * @dataProvider provideMethods
     */
    public function testBasicMethodsFail($method)
    {
        $factoryManager = new FactoryManager();

        $factory = $this->getMock('Buzz\Message\Factory\FactoryInterface');
        try {
            $factoryManager->$method(array(), $factory);
            $this->fail();
        } catch (\Exception $e) {
            $this->assertEquals('$name must be a string', $e->getMessage());
        }

        try {
            $factoryManager->get('test');
            $this->fail();
        } catch (\Exception $e) {
            $this->assertEquals('Buzz message factory with name "test" not found.', $e->getMessage());
        }

    }

    public function provideMethods()
    {
        return array(
            array('get'),
            array('set'),
            array('has'),
        );
    }

    public function testGetIterator()
    {
        $fm = new FactoryManager();
        $fm->set('foo', $this->getMock('Buzz\Message\Factory\FactoryInterface'));
        $fm->set('bar', $this->getMock('Buzz\Message\Factory\FactoryInterface'));

        $this->assertTrue($fm->getIterator() instanceof \ArrayIterator);
        $this->assertEquals(2, count($fm));
    }
}

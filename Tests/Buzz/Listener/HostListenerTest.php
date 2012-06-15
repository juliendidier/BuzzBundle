<?php

namespace Buzz\Bundle\BuzzBundle\Tests\Buzz\Listener;

use Buzz\Bundle\BuzzBundle\Buzz\Listener\HostListener;

class HostListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testPreSend()
    {
        $listener = new HostListener('my://foo');
        $request = $this->getMock('Buzz\Message\Request');

        $request->expects($this->once())
            ->method('setHost')
            ->with($this->isType('string'))
            ->will($this->returnValue('my://foo'));

        $listener->preSend($request);

        $response = $this->getMock('Buzz\Message\MessageInterface');
    }
}

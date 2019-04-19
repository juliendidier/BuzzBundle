<?php

namespace Buzz\Bundle\BuzzBundle\Tests\Buzz\Listener;

use Buzz\Bundle\BuzzBundle\Buzz\Listener\HostListener;
use Buzz\Message\MessageInterface;
use Buzz\Message\Request;
use Buzz\Message\RequestInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class HostListenerTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testPreSend()
    {
        $listener = new HostListener('my://foo');
        /** @var RequestInterface|MockObject $request */
        $request = $this->createMock(Request::class);

        $request->expects($this->once())
            ->method('setHost')
            ->with($this->isType('string'))
            ->will($this->returnValue('my://foo'));

        $listener->preSend($request);

        $response = $this->createMock(MessageInterface::class);
        /** @var RequestInterface|MockObject $cloneResponse */
        $cloneResponse = clone $response;
        $listener->postSend($cloneRequest = clone $request, $cloneResponse);

        $this->assertEquals($request, $cloneRequest, 'postSend does nothing on the request');
        $this->assertEquals($response, $cloneResponse, 'postSend does nothing on the response');
    }
}

<?php

namespace Buzz\Bundle\BuzzBundle\Tests\Buzz;

use Buzz\Message\Message;

use Buzz\Bundle\BuzzBundle\Buzz\Browser;

class BrowserTest extends \PHPUnit_Framework_TestCase
{
    public function testSend()
    {
        $client = $this->getMock('Buzz\Client\ClientInterface');
        $factory = $this->getMock('Buzz\Message\Factory\FactoryInterface');
        $browser = new Browser('http://google.com', $client, $factory);

        $request = $this->getMock('Buzz\Message\RequestInterface');
        $response = $this->getMock('Buzz\Message\MessageInterface');

        $request->expects($this->once())
            ->method('setHost')
            ->will($this->returnValue('http://google.com'));
        $factory->expects($this->once())
            ->method('createResponse')
            ->will($this->returnValue($response))
        ;

        $actual = $browser->send($request);

        $this->assertSame($response, $actual);
    }
}

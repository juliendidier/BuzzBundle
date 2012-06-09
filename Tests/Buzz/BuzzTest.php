<?php

namespace Buzz\Bundle\BuzzBundle\Tests\Buzz\Browser;

use Buzz\Bundle\BuzzBundle\Buzz\BrowserManager;
use Buzz\Bundle\BuzzBundle\Buzz\Buzz;

class BuzzTest extends \PHPUnit_Framework_TestCase
{
    public function testGetBorwser()
    {
        $browserManager = new BrowserManager();
        $buzz = new Buzz($browserManager);
        $foo = $this->getMock('Buzz\Browser');
        $browserManager->set('foo', $foo);

        $this->assertEquals($foo, $buzz->getBrowser('foo'));
    }

    public function testHasBrowser()
    {
        $browserManager = new BrowserManager();
        $buzz = new Buzz($browserManager);
        $foo = $this->getMock('Buzz\Browser');
        $browserManager->set('foo', $foo);

        $this->assertTrue($buzz->hasBrowser('foo'));
    }
}

<?php

namespace Buzz\Bundle\BuzzBundle\Tests\Buzz\Browser;

use Buzz\Browser;
use Buzz\Bundle\BuzzBundle\Buzz\BrowserManager;
use Buzz\Bundle\BuzzBundle\Buzz\Buzz;
use PHPUnit\Framework\TestCase;

class BuzzTest extends TestCase
{
    public function testGetBorwser()
    {
        $browserManager = new BrowserManager();
        $buzz = new Buzz($browserManager);
        $foo = $this->createMock(Browser::class);
        $browserManager->set('foo', $foo);

        $this->assertEquals($foo, $buzz->getBrowser('foo'));
    }

    public function testHasBrowser()
    {
        $browserManager = new BrowserManager();
        $buzz = new Buzz($browserManager);
        $foo = $this->createMock(Browser::class);
        $browserManager->set('foo', $foo);

        $this->assertTrue($buzz->hasBrowser('foo'));
    }
}

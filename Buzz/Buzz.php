<?php

namespace Buzz\Bundle\BuzzBundle\Buzz;

use Buzz\Bundle\BuzzBundle\Buzz\BrowserManager;
use Buzz\Bundle\BuzzBundle\Exception\BuzzException;

class Buzz
{
    protected $browsers;

    public function __construct(BrowserManager $browsers = null, array $config = null)
    {
        $this->browsers = $browsers;
    }

    public function getBrowser($name)
    {
        return $this->browsers->get($name);
    }

    public function hasBrowser($name)
    {
        return $this->browsers->has($name);
    }
}

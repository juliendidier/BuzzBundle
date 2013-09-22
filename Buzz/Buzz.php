<?php

namespace Buzz\Bundle\BuzzBundle\Buzz;

use Buzz\Bundle\BuzzBundle\Buzz\BrowserManager;

class Buzz
{
    protected $browsers;

    public function getBrowser($name)
    {
        return $this->browsers->get($name);
    }

    public function hasBrowser($name)
    {
        return $this->browsers->has($name);
    }
}

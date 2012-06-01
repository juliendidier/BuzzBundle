<?php

namespace Buzz\Bundle\BuzzBundle\Buzz;

use Buzz\Browser;

class BrowserManager
{
    protected $browsers;

    public function set($name, Browser $browser)
    {
        $this->browsers[$name] = $browser;
    }

    public function get($name = null)
    {
        if (!is_string($name)) {
            throw new UnexpectedTypeException($name, 'string');
        }

        return $this->browsers[$name];
    }

    public function has($name)
    {
        if (!is_string($name)) {
            throw new UnexpectedTypeException($name, 'string');
        }

        return isset($this->browsers[$name]);
    }
}

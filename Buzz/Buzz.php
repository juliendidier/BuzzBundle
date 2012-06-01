<?php

namespace Buzz\Bundle\BuzzBundle\Buzz;

use Buzz\Bundle\BuzzBundle\Buzz\BrowserManager;
use Buzz\Bundle\BuzzBundle\Exception\BuzzException;

class Buzz
{
    protected $browsers;

    public function __construct(BrowserManager $browsers = null)
    {
        $this->browsers = $browsers;
    }

    /**
     * Returns a browser by name.
     *
     * @param string $name The name of the browser
     *
     * @return Browser The browser
     *
     * @throws BuzzException if the browser can not be retrieved
     */
    public function getBrowser($name)
    {
        return $this->browsers->get($name);
    }

    /**
     * Returns whether the given browser is supported.
     *
     * @param string $name The name of the browser
     *
     * @return Boolean Whether the browser is supported
     */
    public function hasBrowser($name)
    {
        return $this->browsers->has($name);
    }
}

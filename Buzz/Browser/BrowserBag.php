<?php

namespace Buzz\Bundle\BuzzBundle\Buzz\Browser;

use Symfony\Component\DependencyInjection\Container;

class BrowserBag
{
    protected $browsers;

    public function __construct()
    {
        $this->browsers = array();
    }

    public function has($name)
    {
        return array_key_exists($name, $this->browsers);
    }

    public function get($name)
    {
        if ($this->has($name)) {
            $this->browsers[$name];
        } else {
            throw new \LogicException(sprintf('No browser found with name "%s"', $name));
        }
    }

    public function add($name, $config)
    {
        $this->browsers[$name] = $config;
    }
}

<?php

namespace Buzz\Bundle\BuzzBundle\Buzz;

use Buzz\Browser;

use Buzz\Bundle\BuzzBundle\Buzz\Message\FactoryManagerInterface;

class BrowserManager implements \Countable, \IteratorAggregate
{
    private $browsers  = array();
    private $factories;

    public function __construct(FactoryManagerInterface $factories = null)
    {
        $this->factories = $factories;
    }

    /**
     * Set a browser on the collection.
     *
     * @param string    $name       The name of the browser
     * @param Browser   $browser    The browser instance
     * @param array     $config     The browser configuration
     *
     * @return Boolean  Whether the browser is supported
     */
    public function set($name, Browser $browser)
    {
        $this->browsers[$name] = $browser;
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
    public function get($name = null)
    {
        if (!is_string($name)) {
            throw new \UnexpectedValueException('$name must be a string');
        }

        if (!$this->has($name)) {
            throw new \UnexpectedValueException(sprintf('Buzz browser with name "%s" not found.', $name));
        }

        $browser = $this->browsers[$name];
        if ($this->factories && $this->factories->has($name)) {
            $browser->setMessageFactory($this->factories->get($name));
        }

        return $browser;
    }

    /**
     * Returns whether the given browser is supported.
     *
     * @param string $name The name of the browser
     *
     * @return Boolean Whether the browser is supported
     */
    public function has($name)
    {
        if (!is_string($name)) {
            throw new \UnexpectedValueException('$name must be a string');
        }

        return isset($this->browsers[$name]);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->browsers);
    }

    public function count()
    {
        return count($this->browsers);
    }
}

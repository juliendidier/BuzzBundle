<?php

namespace Buzz\Bundle\BuzzBundle\Buzz;

use Buzz\Bundle\BuzzBundle\DependencyInjection\Factory\Browser\BrowserFactory;
use Buzz\Bundle\BuzzBundle\Exception\BuzzException;

class Buzz
{
    protected $browsers;

    public function __construct(array $browsers = array())
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
        if (!is_string($name)) {
            throw new UnexpectedTypeException($name, 'string');
        }

        if (!isset($this->browsers[$name])) {
            $this->loadBrowser($name);
        }

        return $this->browsers[$name];
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
        if (isset($this->browsers[$name])) {
            return true;
        }

        try {
            $this->loadBrowser($name);
        } catch (BuzzException $e) {
            return false;
        }

        return true;
    }

    /**
     * Loads a browser.
     *
     * @param string $name The browser name
     *
     * @throws BuzzException if the browser is not provided by any registered extension
     */
    private function loadBrowser($name)
    {
        $browser = null;
        foreach ($this->browsers as $key => $value) {
            if ($key === $serviceId) {
                $browser = $value;
                break;
            }
        }

        if (!$browser) {
            throw new BuzzException(sprintf('Could not load browser "%s"', $name));
        }

        $this->browsers[$name] = $browser;
    }

}
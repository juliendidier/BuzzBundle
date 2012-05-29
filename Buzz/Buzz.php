<?php

namespace Buzz\Bundle\BuzzBundle\Buzz;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Buzz\Bundle\BuzzBundle\DependencyInjection\Factory\Browser\BrowserFactory;
use Buzz\Bundle\BuzzBundle\Exception\BuzzException;

class Buzz
{
    protected $container;
    protected $browsers;

    public function __construct(ContainerInterface $container, array $browsers = null)
    {
        $this->container = $container;
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

        return $this->loadBrowser($name);
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
        return isset($this->browsers[$name]) && $this->container->has($this->browsers[$name]);
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
        if (!$this->hasBrowser($name)) {
            throw new BuzzException(sprintf('Could not load browser "%s"', $name));
        }

        $serviceId = $this->browsers[$name];

        return $this->container->get($serviceId);
    }

}

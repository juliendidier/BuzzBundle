<?php

namespace Buzz\Bundle\BuzzBundle\Buzz\Message;

use Buzz\Message\Factory\FactoryInterface;

class FactoryManager
{
    protected $factories;

    /**
     * Set a factory on the collection.
     *
     * @param string    $name       The name of the factory
     * @param Browser   $factory    The factory instance
     *
     * @return Boolean  Whether the factory is supported
     */
    public function set($name, FactoryInterface $factory)
    {
        $this->factories[$name] = $factory;
    }

    /**
     * Returns a factory by name.
     *
     * @param string $name The name of the factory
     *
     * @return Browser The factory
     *
     * @throws BuzzException if the factory can not be retrieved
     */
    public function get($name = null)
    {
        if (!is_string($name)) {
            throw new UnexpectedTypeException($name, 'string');
        }

        if (!$this->has($name)) {
            throw new BuzzException(sprintf('Buzz message factory with name "%s" not found.', $name));
        }

        $factory = $this->factories[$name];

        return $factory;
    }

    /**
     * Returns whether the given factory is supported.
     *
     * @param string $name The name of the factory
     *
     * @return Boolean Whether the factory is supported
     */
    public function has($name)
    {
        if (!is_string($name)) {
            throw new UnexpectedTypeException($name, 'string');
        }

        return isset($this->factories[$name]);
    }
}

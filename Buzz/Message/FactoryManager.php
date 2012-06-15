<?php

namespace Buzz\Bundle\BuzzBundle\Buzz\Message;

use Buzz\Message\Factory\FactoryInterface;

class FactoryManager implements FactoryManagerInterface
{
    private $factories;

    /**
     * Returns a factory by name.
     *
     * @param string $name The name of the factory
     *
     * @return Browser The factory
     *
     * @throws UnexpectedValueException if the factory can not be retrieved
     */
    public function get($name)
    {
        if (!is_string($name)) {
            throw new \UnexpectedValueException('$name must be a string');
        }

        if (!$this->has($name)) {
            throw new \UnexpectedValueException(sprintf('Buzz message factory with name "%s" not found.', $name));
        }

        $factory = $this->factories[$name];

        return $factory;
    }

    /**
     * Set a factory on the collection.
     *
     * @param string    $name       The name of the factory
     * @param Browser   $factory    The factory instance
     *
     * @return Boolean  Whether the factory is supported
     *
     * @throws UnexpectedValueException if the factory can not be retrieved
     */
    public function set($name, FactoryInterface $factory)
    {
        if (!is_string($name)) {
            throw new \UnexpectedValueException('$name must be a string');
        }

        $this->factories[$name] = $factory;
    }

    /**
     * Returns whether the given factory is supported.
     *
     * @param string $name The name of the factory
     *
     * @return Boolean Whether the factory is supported
     *
     * @throws UnexpectedValueException if the factory can not be retrieved
     */
    public function has($name)
    {
        if (!is_string($name)) {
            throw new \UnexpectedValueException('$name must be a string');
        }

        return isset($this->factories[$name]);
    }
}

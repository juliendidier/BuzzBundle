<?php

namespace Buzz\Bundle\BuzzBundle\Buzz\Message;

use Buzz\Message\Factory\FactoryInterface;

interface FactoryManagerInterface extends \Countable, \IteratorAggregate
{
    function get($name);
    function set($name, FactoryInterface $factory);
    function has($name);
}

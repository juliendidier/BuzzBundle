<?php

namespace Buzz\Bundle\BuzzBundle\Buzz\Message;

use Buzz\Message\Factory as BaseFactory;

class Factory extends BaseFactory
{
    protected $host;

    public function __construct($host)
    {
        $this->host = $host;
    }

    public function createRequest($method = RequestInterface::METHOD_GET, $resource = '/', $host = null)
    {
        $host = $host ?: $this->host;

        return parent::createRequest($method, $resource, $host);
    }

    public function createFormRequest($method = RequestInterface::METHOD_POST, $resource = '/', $host = null)
    {
        $host = $host ?: $this->host;

        return parent::createFormRequest($method, $resource, $host);
    }
}

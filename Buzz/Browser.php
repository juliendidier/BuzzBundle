<?php

namespace Buzz\Bundle\BuzzBundle\Buzz;

use Buzz\Browser as BaseBrowser;
use Buzz\Client\ClientInterface;
use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;
use Buzz\Message\Factory\FactoryInterface;
use Buzz\Util\Url;

class Browser extends BaseBrowser
{
    protected $host;

    public function __construct($host, ClientInterface $client = null, FactoryInterface $factory = null)
    {
        parent::__construct($client, $factory);

        $this->host = $host;
    }

    public function send(RequestInterface $request, MessageInterface $response = null)
    {
        $this->prepareRequest($request);

        return parent::send($request, $response);
    }

    protected function prepareRequest(RequestInterface $request)
    {
        if (!$request->getHost()) {
            $request->setHost($this->host);
        }
    }
}

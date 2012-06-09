<?php

namespace Buzz\Bundle\BuzzBundle\Buzz\Browser;

use Buzz\Browser as BaseBrowser;
use Buzz\Client\ClientInterface;
use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;
use Buzz\Message\Factory\FactoryInterface;
use Buzz\Util\Url;

class Browser extends BaseBrowser
{
    protected $host;

    /**
     * Constructor
     *
     * @param $host     string              An host for the browser
     * @param $client   ClientInterface     An host for the browser
     * @param $factory  FactoryInterface    An host for the browser
     */
    public function __construct($host, ClientInterface $client = null, FactoryInterface $factory = null)
    {
        parent::__construct($client, $factory);

        $this->host = $host;
    }

    /**
     * Send request
     *
     * @param $request  RequestInterface    A request
     * @param $client   MessageInterface    A response
     *
     * @return Response
     */
    public function send(RequestInterface $request, MessageInterface $response = null)
    {
        $this->prepareRequest($request);

        return parent::send($request, $response);
    }

    /**
     * Prepare the request before sending
     *
     * @param $request  RequestInterface    A request
     *
     * @return Request
     */
    protected function prepareRequest(RequestInterface $request)
    {
        if (!$request->getHost()) {
            $request->setHost($this->host);
        }

        return $request;
    }
}

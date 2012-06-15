<?php

namespace Buzz\Bundle\BuzzBundle\Buzz\Listener;

use Buzz\Listener\ListenerInterface;
use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;

class HostListener implements ListenerInterface
{
    private $host;

    public function __construct($host)
    {
        $this->host = $host;
    }

    public function preSend(RequestInterface $request)
    {
        $request->setHost($this->host);
    }

    public function postSend(RequestInterface $request, MessageInterface $response)
    {
    }
}

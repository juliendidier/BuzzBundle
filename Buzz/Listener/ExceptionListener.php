<?php

namespace Buzz\Bundle\BuzzBundle\Buzz\Listener;

use Buzz\Listener\ListenerInterface;
use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;

use Buzz\Bundle\BuzzBundle\Exception\ResponseException;

class ExceptionListener implements ListenerInterface
{
    public function preSend(RequestInterface $request)
    {
    }

    public function postSend(RequestInterface $request, MessageInterface $response)
    {
        if (!$response->isSuccessful()) {
            throw new ResponseException($request, $response);
        }
    }
}

<?php

namespace Buzz\Bundle\BuzzBundle\Buzz\Client;

use Buzz\Client\Curl as BaseClient;
use Buzz\Message;

class Client extends BaseClient
{
    public function send(Message\Request $request, Message\Response $response)
    {
        parent::send($request, $response);
    }
}

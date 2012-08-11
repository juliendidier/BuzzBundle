<?php

namespace Buzz\Bundle\BuzzBundle\Exception;

use Buzz\Message\RequestInterface;
use Buzz\Message\Response;

class ResponseException extends \RuntimeException
{
    public function __construct(RequestInterface $request, Response $response)
    {
        $message = sprintf('The request for "%s%s" failed : %s (%d)',
            $request->getHost(),
            $request->getResource(),
            $response->getReasonPhrase(),
            $response->getStatusCode()
        );
        $statusCode = $response->getStatusCode();

        parent::__construct($message, $statusCode);
    }
}

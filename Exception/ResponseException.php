<?php

namespace Buzz\Bundle\BuzzBundle\Exception;

use Buzz\Message\RequestInterface;
use Buzz\Message\Response;

class ResponseException extends \RuntimeException
{
    private $request;
    
    private $response;
    
    public function __construct(RequestInterface $request, Response $response)
    {
        $message = sprintf('The request for "%s%s" failed : %s (%d)',
            $request->getHost(),
            $request->getResource(),
            $response->getReasonPhrase(),
            $response->getStatusCode()
        );
        $statusCode = $response->getStatusCode();
        
        $this->request = $request;
        $this->response = $response;

        parent::__construct($message, $statusCode);
    }

    /**
     * @return RequestInterface 
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Response 
     */
    public function getResponse()
    {
        return $this->response;
    }
}

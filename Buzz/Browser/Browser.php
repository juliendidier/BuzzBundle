<?php

namespace Buzz\Bundle\BuzzBundle\Buzz\Browser;

use Buzz\Browser as BaseBrowser;
use Buzz\Client\ClientInterface;

class Browser extends BaseBrowser
{
    protected $client;

    public function __construct($client)
    {
        $this->client = $client;
    }
}
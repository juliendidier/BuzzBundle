<?php

namespace Buzz\Bundle\BuzzBundle\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

use Buzz\Listener\HistoryListener;

class BuzzDataCollector extends DataCollector
{
    private $listener;

    function __construct(HistoryListener $listener)
    {
        $this->listener = $listener;
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = array();

        foreach ($this->listener->getJournal() as $entry) {
            $this->data[] = array(
                'request' => $entry->getRequest(),
                'response' => $entry->getResponse(),
                'duration' => $entry->getDuration(),
            );
        }
    }

    public function getNbEntries()
    {
        return count($this->data);
    }

    public function getDuration()
    {
        $duration = 0;

        foreach ($this->data as $entry) {
            $duration+= $entry['duration'];
        }

        return $duration;
    }

    public function getData()
    {
        return array_reverse($this->data);
    }

    public function getName()
    {
        return 'buzz';
    }
}

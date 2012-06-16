<?php

namespace Buzz\Bundle\BuzzBundle\Tests\DataCollector;

use Buzz\Listener\HistoryListener;
use Buzz\Listener\History\Entry;
use Buzz\Listener\History\Journal;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Buzz\Bundle\BuzzBundle\DataCollector\BuzzDataCollector;

class BuzzDataCollectorTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!class_exists('Symfony\Component\HttpFoundation\Request')) {
            $this->markTestSkipped('The "HttpFoundation" component is not available');
        }
    }

    public function testCollect()
    {
        $journal = new Journal();
        $history = new HistoryListener($journal);
        $c = new BuzzDataCollector($history);

        $data = array();
        foreach ($this->getTestEntry() as $entry) {
            $journal->addEntry($entry);
            $data[] = array(
                'request' => $entry->getRequest(),
                'response' => $entry->getResponse(),
                'duration' => $entry->getDuration()
            );
        }

        $c->collect(new Request(), new Response());

        $this->assertSame('buzz', $c->getName());
        $this->assertSame(11, $c->getDuration());
        $this->assertSame(2, $c->getNbEntries());
        $this->assertEquals(array_reverse($data), $c->getData());
    }

    public function getTestEntry()
    {
        return array(
            new Entry($this->getMock('Buzz\Message\Request'), $this->getMock('Buzz\Message\Response'), 2),
            new Entry($this->getMock('Buzz\Message\Request'), $this->getMock('Buzz\Message\Response'), 9),
        );
    }
}


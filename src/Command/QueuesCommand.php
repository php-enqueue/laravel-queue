<?php
namespace Enqueue\LaravelQueue\Command;

use Enqueue\SimpleClient\SimpleClient;

class QueuesCommand extends \Enqueue\Symfony\Client\Meta\QueuesCommand
{
    public function __construct(SimpleClient $client)
    {
        parent::__construct($client->getQueueMetaRegistry());
    }
}

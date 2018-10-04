<?php
namespace Enqueue\LaravelQueue\Command;

use Enqueue\Container\Container;
use Enqueue\SimpleClient\SimpleClient;

class ConsumeCommand extends \Enqueue\Symfony\Client\ConsumeCommand
{
    public function __construct(SimpleClient $client)
    {
        $container = new Container([
            'queue_consumer' => $client->getQueueConsumer(),
            'driver' => $client->getDriver(),
            'processor' => $client->getDelegateProcessor()
        ]);

        parent::__construct($container, 'queue_consumer', 'driver', 'processor');
    }
}

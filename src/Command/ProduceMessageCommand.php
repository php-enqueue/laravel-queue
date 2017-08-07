<?php
namespace Enqueue\LaravelQueue\Command;

use Enqueue\SimpleClient\SimpleClient;

class ProduceMessageCommand extends \Enqueue\Symfony\Client\ProduceMessageCommand
{
    public function __construct(SimpleClient $client)
    {
        parent::__construct($client->getProducer());
    }
}

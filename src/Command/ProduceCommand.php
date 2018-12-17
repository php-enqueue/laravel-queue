<?php
namespace Enqueue\LaravelQueue\Command;

use Enqueue\SimpleClient\SimpleClient;
use Enqueue\Symfony\Client\SimpleProduceCommand;

class ProduceCommand extends SimpleProduceCommand
{
    public function __construct(SimpleClient $client)
    {
        parent::__construct($client->getProducer());
    }
}

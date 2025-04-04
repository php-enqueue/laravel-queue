<?php
namespace Enqueue\LaravelQueue\Command;

use Enqueue\SimpleClient\SimpleClient;
use Enqueue\Symfony\Client\SimpleProduceCommand;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'enqueue:produce')]
class ProduceCommand extends SimpleProduceCommand
{
    public function __construct(SimpleClient $client)
    {
        parent::__construct($client->getProducer());
    }
}

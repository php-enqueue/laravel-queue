<?php
namespace Enqueue\LaravelQueue\Command;

use Enqueue\SimpleClient\SimpleClient;
use Enqueue\Symfony\Client\SimpleConsumeCommand;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'enqueue:consume')]
class ConsumeCommand extends SimpleConsumeCommand
{
    public function __construct(SimpleClient $client)
    {
        parent::__construct(
            $client->getQueueConsumer(),
            $client->getDriver(),
            $client->getDelegateProcessor()
        );
    }
}

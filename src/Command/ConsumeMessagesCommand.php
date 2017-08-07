<?php
namespace Enqueue\LaravelQueue\Command;

use Enqueue\SimpleClient\SimpleClient;

class ConsumeMessagesCommand extends \Enqueue\Symfony\Client\ConsumeMessagesCommand
{
    public function __construct(SimpleClient $client)
    {
        parent::__construct(
            $client->getQueueConsumer(),
            $client->getDelegateProcessor(),
            $client->getQueueMetaRegistry(),
            $client->getDriver()
        );
    }
}

<?php
namespace Enqueue\LaravelQueue\Command;

use Enqueue\SimpleClient\SimpleClient;

class TopicsCommand extends \Enqueue\Symfony\Client\Meta\TopicsCommand
{
    public function __construct(SimpleClient $client)
    {
        parent::__construct($client->getTopicMetaRegistry());
    }
}

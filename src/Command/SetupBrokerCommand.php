<?php
namespace Enqueue\LaravelQueue\Command;

use Enqueue\SimpleClient\SimpleClient;
use Enqueue\Symfony\Client\SimpleSetupBrokerCommand;

class SetupBrokerCommand extends SimpleSetupBrokerCommand
{
    public function __construct(SimpleClient $client)
    {
        parent::__construct($client->getDriver());
    }
}
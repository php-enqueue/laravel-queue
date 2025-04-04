<?php
namespace Enqueue\LaravelQueue\Command;

use Enqueue\SimpleClient\SimpleClient;
use Enqueue\Symfony\Client\SimpleSetupBrokerCommand;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('enqueue:setup-broker')]
class SetupBrokerCommand extends SimpleSetupBrokerCommand
{
    public function __construct(SimpleClient $client)
    {
        parent::__construct($client->getDriver());
    }
}

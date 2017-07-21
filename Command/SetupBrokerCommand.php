<?php
namespace Enqueue\LaravelQueue\Command;


use Enqueue\SimpleClient\SimpleClient;

class SetupBrokerCommand extends \Enqueue\Symfony\Client\SetupBrokerCommand
{
    public function __construct(SimpleClient $client)
    {
        parent::__construct($client->getDriver());
    }
}
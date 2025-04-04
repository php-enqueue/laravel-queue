<?php
namespace Enqueue\LaravelQueue\Command;

use Enqueue\SimpleClient\SimpleClient;
use Enqueue\Symfony\Client\SimpleRoutesCommand;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'enqueue:routes')]
class RoutesCommand extends SimpleRoutesCommand
{
    public function __construct(SimpleClient $client)
    {
        parent::__construct($client->getDriver());
    }
}

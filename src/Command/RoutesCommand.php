<?php
namespace Enqueue\LaravelQueue\Command;

use Enqueue\SimpleClient\SimpleClient;
use Enqueue\Symfony\Client\SimpleRoutesCommand;

class RoutesCommand extends SimpleRoutesCommand
{
    public function __construct(SimpleClient $client)
    {
        parent::__construct($client->getDriver());
    }
}

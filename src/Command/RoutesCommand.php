<?php
namespace Enqueue\LaravelQueue\Command;

use Enqueue\Container\Container;
use Enqueue\SimpleClient\SimpleClient;

class RoutesCommand extends \Enqueue\Symfony\Client\RoutesCommand
{
    public function __construct(SimpleClient $client)
    {
        $container = new Container([
            'driver' => $client->getDriver(),
        ]);

        parent::__construct($container, 'driver');
    }
}

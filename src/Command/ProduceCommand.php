<?php
namespace Enqueue\LaravelQueue\Command;

use Enqueue\Container\Container;
use Enqueue\SimpleClient\SimpleClient;

class ProduceCommand extends \Enqueue\Symfony\Client\ProduceCommand
{
    public function __construct(SimpleClient $client)
    {
        $container = new Container([
            'producer' => $client->getProducer(),
        ]);

        parent::__construct($container, 'producer');
    }
}

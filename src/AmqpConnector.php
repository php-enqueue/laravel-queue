<?php

namespace Enqueue\LaravelQueue;

use Enqueue\AmqpTools\DelayStrategyAware;
use Enqueue\AmqpTools\RabbitMqDlxDelayStrategy;
use Interop\Amqp\AmqpContext;

class AmqpConnector extends Connector
{
    public function connect(array $config)
    {
        $queue = parent::connect($config);

        /** @var AmqpContext $amqpContext */
        $amqpContext = $queue->getPsrContext();
        if (false == $amqpContext instanceof AmqpContext) {
            throw new \LogicException(sprintf('The context must be instance of "%s" but got "%s"', AmqpContext::class, get_class($queue->getPsrContext())));
        }

        if ($amqpContext instanceof DelayStrategyAware) {
            $amqpContext->setDelayStrategy(new RabbitMqDlxDelayStrategy());
        }

        return $queue;
    }
}

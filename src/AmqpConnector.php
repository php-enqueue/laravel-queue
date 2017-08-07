<?php

namespace Enqueue\LaravelQueue;

use Interop\Amqp\AmqpContext;

class AmqpConnector extends Connector
{
    public function connect(array $config)
    {
        $queue = parent::connect($config);

        if (false == $queue->getPsrContext() instanceof AmqpContext) {
            throw new \LogicException(sprintf('The context must be instance of "%s" but got "%s"', AmqpContext::class, get_class($queue->getPsrContext()));
        }

        // TODO set delay strategy.

        return $queue;
    }
}

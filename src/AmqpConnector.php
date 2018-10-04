<?php

namespace Enqueue\LaravelQueue;

use Enqueue\AmqpTools\DelayStrategy;
use Enqueue\AmqpTools\DelayStrategyAware;
use Enqueue\AmqpTools\RabbitMqDelayPluginDelayStrategy;
use Enqueue\AmqpTools\RabbitMqDlxDelayStrategy;
use Interop\Amqp\AmqpContext;

class AmqpConnector extends Connector
{
    public function connect(array $config)
    {
        $queue = parent::connect($config);

        $config = array_replace(['delay_strategy' => 'rabbitmq_dlx'], $config);

        /** @var AmqpContext $amqpContext */
        $amqpContext = $queue->getQueueInteropContext();
        if (false == $amqpContext instanceof AmqpContext) {
            throw new \LogicException(sprintf('The context must be instance of "%s" but got "%s"', AmqpContext::class, get_class($queue->getQueueInteropContext())));
        }

        if ($amqpContext instanceof DelayStrategyAware && 'rabbitmq_dlx' == $config['delay_strategy']) {
            $amqpContext->setDelayStrategy(new RabbitMqDlxDelayStrategy());
        }
        if ($amqpContext instanceof DelayStrategyAware && 'rabbitmq_delay_plugin' == $config['delay_strategy']) {
            $amqpContext->setDelayStrategy(new RabbitMqDelayPluginDelayStrategy());
        }
        if ($amqpContext instanceof DelayStrategyAware && $config['delay_strategy'] instanceof DelayStrategy) {
            $amqpContext->setDelayStrategy($config['delay_strategy']);
        }

        return $queue;
    }
}

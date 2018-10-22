<?php

namespace Enqueue\LaravelQueue;

use Enqueue\AmqpTools\DelayStrategy;
use Enqueue\AmqpTools\DelayStrategyAware;
use Enqueue\AmqpTools\RabbitMqDelayPluginDelayStrategy;
use Enqueue\AmqpTools\RabbitMqDlxDelayStrategy;
use Enqueue\ConnectionFactoryFactory;
use Enqueue\ConnectionFactoryFactoryInterface;
use Illuminate\Contracts\Queue\Queue as QueueContract;
use Illuminate\Queue\Connectors\ConnectorInterface;
use Interop\Amqp\AmqpContext;

class Connector implements ConnectorInterface
{
    public function connect(array $config): QueueContract
    {
        $config = array_replace([
            'dsn' => null,
            'factory_class' => null,
            'queue' => 'default',
            'time_to_run' => 0,
        ], $config);

        $queue = $config['queue'];
        $timeToRum = $config['time_to_run'];
        $connectionFactoryFactoryClass = $config['factory_class'] ?? ConnectionFactoryFactory::class;

        unset($config['factory_class']);

        /** @var ConnectionFactoryFactoryInterface $factory */
        $factory = new $connectionFactoryFactoryClass();
        $connection = $factory->create($config);
        $context = $connection->createContext();

        if ($context instanceof AmqpContext) {
            $config = array_replace(['delay_strategy' => 'rabbitmq_dlx'], $config);

            if ($context instanceof DelayStrategyAware && 'rabbitmq_dlx' == $config['delay_strategy']) {
                $context->setDelayStrategy(new RabbitMqDlxDelayStrategy());
            }
            if ($context instanceof DelayStrategyAware && 'rabbitmq_delay_plugin' == $config['delay_strategy']) {
                $context->setDelayStrategy(new RabbitMqDelayPluginDelayStrategy());
            }
            if ($context instanceof DelayStrategyAware && $config['delay_strategy'] instanceof DelayStrategy) {
                $context->setDelayStrategy($config['delay_strategy']);
            }

            return new AmqpQueue($context, $queue, $timeToRum);
        }

        return new Queue($context, $queue, $timeToRum);
    }
}

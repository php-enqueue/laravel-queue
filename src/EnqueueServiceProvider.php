<?php

namespace Enqueue\LaravelQueue;

use Enqueue\LaravelQueue\Command\ConsumeMessagesCommand;
use Enqueue\LaravelQueue\Command\ProduceMessageCommand;
use Enqueue\LaravelQueue\Command\QueuesCommand;
use Enqueue\LaravelQueue\Command\SetupBrokerCommand;
use Enqueue\LaravelQueue\Command\TopicsCommand;
use Enqueue\SimpleClient\SimpleClient;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\ServiceProvider;

class EnqueueServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->bootInteropQueueDriver();
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->registerClient();
    }

    private function registerClient()
    {
        if (false == $this->app['config']->has('enqueue.client')) {
            return;
        }

        if (false == class_exists(SimpleClient::class)) {
            throw new \LogicException('The enqueue/simple-client package is not installed');
        }

        $this->app->singleton(SimpleClient::class, function() {
            /** @var \Illuminate\Config\Repository $config */
            $config = $this->app['config'];
            
            return new SimpleClient($config->get('enqueue.client'));
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                SetupBrokerCommand::class,
                ProduceMessageCommand::class,
                QueuesCommand::class,
                TopicsCommand::class,
                ConsumeMessagesCommand::class,
            ]);
        }
    }

    private function bootInteropQueueDriver()
    {
        /** @var QueueManager $manager */
        $manager = $this->app['queue'];

        $manager->addConnector('interop', function () {
            return new Connector();
        });

        $manager->addConnector('amqp_interop', function () {
            return new AmqpConnector();
        });
    }
}

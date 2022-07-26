<?php

namespace Enqueue\LaravelQueue;

use Enqueue\LaravelQueue\Command\ConsumeCommand;
use Enqueue\LaravelQueue\Command\ProduceCommand;
use Enqueue\LaravelQueue\Command\RoutesCommand;
use Enqueue\LaravelQueue\Command\SetupBrokerCommand;
use Enqueue\SimpleClient\SimpleClient;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\ServiceProvider;

class EnqueueServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $extensions = [];

    public function boot()
    {
        $this->bootInteropQueueDriver();
    }

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
                ProduceCommand::class,
                RoutesCommand::class,
                ConsumeCommand::class,
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
            return new Connector();
        });

        $this->app->extend('queue.worker', function ($worker, $app) {
            return (new Worker(
                $app['queue'],
                $app['events'],
                $app[ExceptionHandler::class],
                function () use ($app) {
                    return $app->isDownForMaintenance();
                }
            ))->setExtensions($this->extensions);
        });
    }
}

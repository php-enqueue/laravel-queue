<?php

namespace Enqueue\LaravelQueue;

use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\Job as JobContract;
use Illuminate\Queue\Jobs\Job as BaseJob;
use Interop\Queue\Consumer;
use Interop\Queue\Context;
use Interop\Queue\Exception\DeliveryDelayNotSupportedException;
use Interop\Queue\Message;
use ReflectionClass;

class Job extends BaseJob implements JobContract
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var Consumer
     */
    private $consumer;

    /**
     * @var Message
     */
    private $message;

    public function __construct(Container $container, Context $context, Consumer $consumer, Message $message, $connectionName)
    {
        $this->container = $container;
        $this->context = $context;
        $this->consumer = $consumer;
        $this->message = $message;
        $this->connectionName = $connectionName;
    }

    public function getJobId()
    {
        return $this->message->getMessageId();
    }

    /**
     * {@inheritdoc}
     */
    public function delete()
    {
        parent::delete();

        $this->consumer->acknowledge($this->message);
    }

    /**
     * {@inheritdoc}
     */
    public function fire()
    {
        $handlerClass = config('queue.connections.interop.handler');

        if (! empty($handlerClass)) {
            return (new $handlerClass($this->consumer->receive()))->handle();
        } else {
            return parent::fire();
        }
    }

    /**
     * In case the payload is not a serialised PHP message, provide a default fall back
     *
     * @return array
     * @throws \ReflectionException
     */
    public function payload()
    {
        if (empty(parent::payload())) {
            $handlerClass = config('queue.connections.interop.handler');

            if (! empty($handlerClass)) {
                return [
                    'job' =>  $handlerClass
                ];
            } else {
                $className = new ReflectionClass(get_class($this));

                return [
                    'job' => dirname($className->getFileName())
                ];
            }
        }

        return parent::payload();
    }

    /**
     * {@inheritdoc}
     */
    public function release($delay = 0)
    {
        parent::release($delay);

        $requeueMessage = clone $this->message;
        $requeueMessage->setProperty('x-attempts', $this->attempts() + 1);

        $producer = $this->context->createProducer();

        try {
            $producer->setDeliveryDelay($this->secondsUntil($delay) * 1000);
        } catch (DeliveryDelayNotSupportedException $e) {
        }

        $this->consumer->acknowledge($this->message);
        $producer->send($this->consumer->getQueue(), $requeueMessage);
    }

    public function getQueue()
    {
        return $this->consumer->getQueue()->getQueueName();
    }

    public function attempts()
    {
        return $this->message->getProperty('x-attempts', 1);
    }

    public function getRawBody()
    {
        return $this->message->getBody();
    }
}

<?php

namespace Enqueue\LaravelQueue;

use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\Job as JobContract;
use Illuminate\Queue\Jobs\Job as BaseJob;
use Interop\Queue\Consumer;
use Interop\Queue\Context;
use Interop\Queue\Message;

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
    public function release($delay = 0)
    {
        if ($delay) {
            throw new \LogicException('To be implemented');
        }

        $requeueMessage = clone $this->message;
        $requeueMessage->setProperty('x-attempts', $this->attempts() + 1);

        $this->context->createProducer()->send($this->consumer->getQueue(), $requeueMessage);

        $this->consumer->acknowledge($this->message);
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

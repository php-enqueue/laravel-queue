<?php

namespace Enqueue\LaravelQueue;

use Illuminate\Contracts\Queue\Queue as QueueContract;
use Illuminate\Queue\Queue as BaseQueue;
use Interop\Queue\Consumer;
use Interop\Queue\Context;
use Interop\Amqp\Impl\AmqpMessage;
use Interop\Queue\Message;

class Queue extends BaseQueue implements QueueContract
{
    /**
     * @var string
     */
    protected $queueName;

    /**
     * @var int
     */
    protected $timeToRun;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @param Context $amqpContext
     * @param string  $queueName
     * @param int     $timeToRun
     */
    public function __construct(Context $amqpContext, $queueName, $timeToRun)
    {
        $this->context = $amqpContext;
        $this->queueName = $queueName;
        $this->timeToRun = $timeToRun;
    }

    /**
     * {@inheritdoc}
     */
    public function size($queue = null)
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function push($job, $data = '', $queue = null)
    {
        return $this->pushRaw($this->createPayload($job, $data), $queue);
    }

    /**
     * {@inheritdoc}
     */
    public function pushRaw($payload, $queue = null, array $options = [])
    {
        $message = $this->context->createMessage($payload);

        if ($message instanceof AmqpMessage) {
            $message->setDeliveryMode(\Interop\Amqp\AmqpMessage::DELIVERY_MODE_PERSISTENT);
        }

        return $this->context->createProducer()->send(
            $this->getQueue($queue),
            $message
        );
    }

    /**
     * {@inheritdoc}
     */
    public function later($delay, $job, $data = '', $queue = null)
    {
        $message = $this->context->createMessage($this->createPayload($job, $data));

        if ($message instanceof AmqpMessage) {
            $message->setDeliveryMode(\Interop\Amqp\AmqpMessage::DELIVERY_MODE_PERSISTENT);
        }

        return $this->context->createProducer()
            ->setDeliveryDelay($this->secondsUntil($delay) * 1000)
            ->send($this->getQueue($queue), $message);
    }

    public function pop($queue = null)
    {
        $queue = $this->getQueue($queue);

        $consumer = $this->context->createConsumer($queue);
        if ($message = $consumer->receive(1000)) { // 1 sec
            return $this->convertMessageToJob($message, $consumer);
        }
    }
    
    public function convertMessageToJob(Message $message, Consumer $consumer): Job
    {
        return new Job(
            $this->container,
            $this->context,
            $consumer,
            $message,
            $this->connectionName
        );
    }

    /**
     * Get the queue or return the default.
     *
     * @param string|null $queue
     *
     * @return \Interop\Queue\Queue
     */
    public function getQueue($queue = null)
    {
        return $this->context->createQueue($queue ?: $this->queueName);
    }

    /**
     * @return Context
     */
    public function getQueueInteropContext()
    {
        return $this->context;
    }

    /**
     * @return int
     */
    public function getTimeToRun()
    {
        return $this->timeToRun;
    }
}

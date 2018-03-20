<?php

namespace Enqueue\LaravelQueue;

use Illuminate\Contracts\Queue\Queue as QueueContract;
use Illuminate\Queue\Queue as BaseQueue;
use Interop\Queue\PsrContext;

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
     * @var PsrContext
     */
    protected $psrContext;

    /**
     * @var boolean
     */
    protected $persistent = false;

    /**
     * @param PsrContext $psrContext
     * @param string     $queueName
     * @param int        $timeToRun
     */
    public function __construct(PsrContext $psrContext, $queueName, $timeToRun)
    {
        $this->psrContext = $psrContext;
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
        $this->persistent = $job->persistent ?? false;

        return $this->pushRaw($this->createPayload($job, $data), $queue);
    }

    /**
     * {@inheritdoc}
     */
    public function pushRaw($payload, $queue = null, array $options = [])
    {
        $message = $this->psrContext->createMessage($payload);

        if ($this->persistent) {
            $message->setDeliveryMode(\Interop\Amqp\AmqpMessage::DELIVERY_MODE_PERSISTENT);
        }

        return $this->psrContext->createProducer()->send(
            $this->getQueue($queue),
            $message
        );
    }

    /**
     * {@inheritdoc}
     */
    public function later($delay, $job, $data = '', $queue = null)
    {
        $message = $this->psrContext->createMessage($this->createPayload($job, $data));

        if (isset($job->persistent) && $job->persistent) {
            $message->setDeliveryMode(\Interop\Amqp\AmqpMessage::DELIVERY_MODE_PERSISTENT);
        }

        return $this->psrContext->createProducer()
            ->setDeliveryDelay($this->secondsUntil($delay) * 1000)
            ->send($this->getQueue($queue), $message);
    }

    /**
     * {@inheritdoc}
     */
    public function pop($queue = null)
    {
        $queue = $this->getQueue($queue);

        $psrConsumer = $this->psrContext->createConsumer($queue);
        if ($psrMessage = $psrConsumer->receive(1000)) { // 1 sec
            return new Job(
                $this->container,
                $this->psrContext,
                $psrConsumer,
                $psrMessage,
                $this->connectionName
            );
        }
    }

    /**
     * Get the queue or return the default.
     *
     * @param string|null $queue
     *
     * @return \Interop\Queue\PsrQueue
     */
    public function getQueue($queue = null)
    {
        return $this->psrContext->createQueue($queue ?: $this->queueName);
    }

    /**
     * @return PsrContext
     */
    public function getPsrContext()
    {
        return $this->psrContext;
    }

    /**
     * @return int
     */
    public function getTimeToRun()
    {
        return $this->timeToRun;
    }
}

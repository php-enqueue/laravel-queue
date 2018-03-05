<?php

namespace Enqueue\LaravelQueue;

use Interop\Amqp\AmqpContext;

/**
 * @method AmqpContext getPsrContext()
 */
class AmqpQueue extends Queue
{
    /**
     * {@inheritdoc}
     *
     * @param AmqpContext $psrContext
     */
    public function __construct(AmqpContext $psrContext, $queueName, $timeToRun)
    {
        parent::__construct($psrContext, $queueName, $timeToRun);
    }

    /**
     * {@inheritdoc}
     */
    public function pushRaw($payload, $queue = null, array $options = [])
    {
        $this->declareQueue($queue);

        parent::pushRaw($payload, $queue, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function later($delay, $job, $data = '', $queue = null)
    {
        $this->declareQueue($queue);

        return parent::later($delay, $job, $data, $queue);
    }

    /**
     * {@inheritdoc}
     */
    public function pop($queue = null)
    {
        $this->declareQueue($queue);

        return parent::pop($queue);
    }

    /**
     * @param string|null $queue
     */
    protected function declareQueue($queue = null)
    {
        $psrQueue = $this->getQueue($queue);
        $psrQueue->addFlag(\Interop\Amqp\AmqpQueue::FLAG_DURABLE);

        $this->getPsrContext()->declareQueue($psrQueue);
    }
}

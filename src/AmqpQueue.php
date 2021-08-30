<?php

namespace Enqueue\LaravelQueue;

use Interop\Amqp\AmqpContext;

/**
 * @method AmqpContext getQueueInteropContext()
 */
class AmqpQueue extends Queue
{
    /**
     * @var int
     */
    protected $size = 0;

    /**
     * {@inheritdoc}
     *
     * @param AmqpContext $amqpContext
     */
    public function __construct(AmqpContext $amqpContext, $queueName, $timeToRun)
    {
        parent::__construct($amqpContext, $queueName, $timeToRun);
    }

    /**
     * {@inheritdoc}
     */
    public function size($queue = null)
    {
        $this->declareQueue($queue);

        return $this->size;
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
        $interopQueue = $this->getQueue($queue);
        $interopQueue->addFlag(\Interop\Amqp\AmqpQueue::FLAG_DURABLE);

        $this->size = $this->getQueueInteropContext()->declareQueue($interopQueue);
    }
}

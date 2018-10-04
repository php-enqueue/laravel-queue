<?php

namespace Enqueue\LaravelQueue\Tests;

use Enqueue\LaravelQueue\Job;
use Enqueue\LaravelQueue\Queue;
use Enqueue\Null\NullMessage;
use Enqueue\Null\NullQueue;
use Enqueue\Test\ClassExtensionTrait;
use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\Queue as QueueContract;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Queue as BaseQueue;
use Interop\Queue\Consumer as InteropConsumer;
use Interop\Queue\Context as InteropContext;
use Interop\Queue\Message as InteropMessage;
use Interop\Queue\Producer as InteropProducer;
use Interop\Queue\Queue as InteropQueue;
use PHPUnit\Framework\TestCase;

class QueueTest extends TestCase
{
    use ClassExtensionTrait;

    public function testShouldImplementsQueueContract()
    {
        $this->assertClassExtends(QueueContract::class, Queue::class);
    }

    public function testShouldExtendsBaseQueue()
    {
        $this->assertClassExtends(BaseQueue::class, Queue::class);
    }

    public function testCouldBeConstructedWithExpectedArguments()
    {
        new Queue($this->createInteropContextMock(), 'queueName', 123);
    }

    public function testShouldReturnInteropContextSetInConstructor()
    {
        $interopContext = $this->createInteropContextMock();

        $queue = new Queue($interopContext, 'queueName', 123);

        $this->assertSame($interopContext, $queue->getQueueInteropContext());
    }

    public function testShouldReturnTimeToRunSetInConstructor()
    {
        $interopContext = $this->createInteropContextMock();

        $queue = new Queue($interopContext, 'queueName', 123);

        $this->assertSame(123, $queue->getTimeToRun());
    }

    public function testShouldReturnDefaultQueueIfNotNameProvided()
    {
        $interopQueue = new NullQueue('queueName');

        $interopContext = $this->createInteropContextMock();
        $interopContext
            ->expects($this->once())
            ->method('createQueue')
            ->with('queueName')
            ->willReturn($interopQueue)
        ;

        $queue = new Queue($interopContext, 'queueName', 123);

        $this->assertSame($interopQueue, $queue->getQueue());
    }

    public function testShouldReturnCustomQueueIfNameProvided()
    {
        $interopQueue = new NullQueue('theCustomQueueName');

        $interopContext = $this->createInteropContextMock();
        $interopContext
            ->expects($this->once())
            ->method('createQueue')
            ->with('theCustomQueueName')
            ->willReturn($interopQueue)
        ;

        $queue = new Queue($interopContext, 'queueName', 123);

        $this->assertSame($interopQueue, $queue->getQueue('theCustomQueueName'));
    }

    public function testShouldSendJobAsMessageToExpectedQueue()
    {
        $interopQueue = new NullQueue('theCustomQueueName');

        $interopProducer = $this->createMock(InteropProducer::class);
        $interopProducer
            ->expects($this->once())
            ->method('send')
            ->willReturnCallback(function (InteropQueue $queue, InteropMessage $message) {
                $this->assertSame('theCustomQueueName', $queue->getQueueName());

                $this->assertContains('"displayName":"Enqueue\\\LaravelQueue\\\Tests\\\TestJob"', $message->getBody());
                $this->assertSame([], $message->getProperties());
                $this->assertSame([], $message->getHeaders());
            })
        ;

        $interopContext = $this->createInteropContextMock();
        $interopContext
            ->expects($this->once())
            ->method('createQueue')
            ->with('theCustomQueueName')
            ->willReturn($interopQueue)
        ;
        $interopContext
            ->expects($this->once())
            ->method('createProducer')
            ->willReturn($interopProducer)
        ;
        $interopContext
            ->expects($this->once())
            ->method('createMessage')
            ->willReturnCallback(function ($body, $properties, $headers) {
                return new NullMessage($body, $properties, $headers);
            })
        ;

        $queue = new Queue($interopContext, 'queueName', 123);

        $queue->push(new TestJob(), '', 'theCustomQueueName');
    }

    public function testShouldSendDoRawPush()
    {
        $interopQueue = new NullQueue('theCustomQueueName');

        $interopProducer = $this->createMock(InteropProducer::class);
        $interopProducer
            ->expects($this->once())
            ->method('send')
            ->willReturnCallback(function (InteropQueue $queue, InteropMessage $message) {
                $this->assertSame('theCustomQueueName', $queue->getQueueName());

                $this->assertSame('thePayload', $message->getBody());
                $this->assertSame([], $message->getProperties());
                $this->assertSame([], $message->getHeaders());
            })
        ;

        $interopContext = $this->createInteropContextMock();
        $interopContext
            ->expects($this->once())
            ->method('createQueue')
            ->with('theCustomQueueName')
            ->willReturn($interopQueue)
        ;
        $interopContext
            ->expects($this->once())
            ->method('createProducer')
            ->willReturn($interopProducer)
        ;
        $interopContext
            ->expects($this->once())
            ->method('createMessage')
            ->willReturnCallback(function ($body, $properties, $headers) {
                return new NullMessage($body, $properties, $headers);
            })
        ;

        $queue = new Queue($interopContext, 'queueName', 123);

        $queue->pushRaw('thePayload', 'theCustomQueueName');
    }

    public function testShouldReturnNullIfNoMessageInQueue()
    {
        $interopQueue = new NullQueue('theCustomQueueName');

        $interopConsumer = $this->createMock(InteropConsumer::class);
        $interopConsumer
            ->expects($this->once())
            ->method('receive')
            ->with(1000)
            ->willReturn(null)
        ;

        $interopContext = $this->createInteropContextMock();
        $interopContext
            ->expects($this->once())
            ->method('createQueue')
            ->with('theCustomQueueName')
            ->willReturn($interopQueue)
        ;
        $interopContext
            ->expects($this->once())
            ->method('createConsumer')
            ->with($this->identicalTo($interopQueue))
            ->willReturn($interopConsumer)
        ;

        $queue = new Queue($interopContext, 'queueName', 123);

        $this->assertNull($queue->pop('theCustomQueueName'));
    }

    public function testShouldReturnJobForReceivedMessage()
    {
        $interopQueue = new NullQueue('theCustomQueueName');
        $interopMessage = new NullMessage();

        $interopConsumer = $this->createMock(InteropConsumer::class);
        $interopConsumer
            ->expects($this->once())
            ->method('receive')
            ->with(1000)
            ->willReturn($interopMessage)
        ;

        $interopContext = $this->createInteropContextMock();
        $interopContext
            ->expects($this->once())
            ->method('createQueue')
            ->with('theCustomQueueName')
            ->willReturn($interopQueue)
        ;
        $interopContext
            ->expects($this->once())
            ->method('createConsumer')
            ->with($this->identicalTo($interopQueue))
            ->willReturn($interopConsumer)
        ;

        $queue = new Queue($interopContext, 'queueName', 123);
        $queue->setContainer(new Container());

        $job = $queue->pop('theCustomQueueName');

        $this->assertInstanceOf(Job::class, $job);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|InteropContext
     */
    private function createInteropContextMock()
    {
        return $this->createMock(InteropContext::class);
    }
}

class TestJob implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle()
    {
    }
}

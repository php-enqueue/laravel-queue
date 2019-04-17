# Laravel queue package

[![Gitter](https://badges.gitter.im/php-enqueue/Lobby.svg)](https://gitter.im/php-enqueue/Lobby)
 
You can use all transports built on top of [queue-interop](https://github.com/queue-interop/queue-interop) including [all supported](https://github.com/php-enqueue/enqueue-dev/tree/master/docs/transport) by Enqueue.
It also supports extended AMQP features such as queue declaration and message delaying.    

The package allows you to use queue interop transport the [laravel way](https://github.com/php-enqueue/enqueue-dev/blob/master/docs/laravel/queues.md) as well as integrates the [enqueue simple client](https://github.com/php-enqueue/enqueue-dev/blob/master/docs/laravel/quick_tour.md#enqueue-simple-client).


## Advantages

* Supports message delaying, priorities and expiration
* Use DSN to configure transport. 12 factors friendly.
* It brings support of a lot of MQ transport with few lines of integration code:

    * [AMQP(s)](https://github.com/php-enqueue/enqueue-dev/blob/master/docs/transport/amqp.md) based on [PHP AMQP extension](https://github.com/pdezwart/php-amqp).
    * [AMQP](https://github.com/php-enqueue/enqueue-dev/blob/master/docs/transport/amqp_bunny.md) based on [bunny](https://github.com/jakubkulhan/bunny). 
    * [AMQP(s)](https://github.com/php-enqueue/enqueue-dev/blob/master/docs/transport/amqp_lib.md) based on [php-amqplib](https://github.com/php-amqplib/php-amqplib). 
    * [Beanstalk](https://github.com/php-enqueue/enqueue-dev/blob/master/docs/transport/pheanstalk.md).
    * [STOMP](https://github.com/php-enqueue/enqueue-dev/blob/master/docs/transport/stomp.md)
    * [Amazon SQS](https://github.com/php-enqueue/enqueue-dev/blob/master/docs/transport/sqs.md)
    * [Google PubSub](https://github.com/php-enqueue/enqueue-dev/blob/master/docs/transport/gps.md)
    * [Kafka](https://github.com/php-enqueue/enqueue-dev/blob/master/docs/transport/kafka.md)
    * [Redis](https://github.com/php-enqueue/enqueue-dev/blob/master/docs/transport/redis.md)
    * [Gearman](https://github.com/php-enqueue/enqueue-dev/blob/master/docs/transport/gearman.md)
    * [Doctrine DBAL](https://github.com/php-enqueue/enqueue-dev/blob/master/docs/transport/dbal.md)
    * [Filesystem](https://github.com/php-enqueue/enqueue-dev/blob/master/docs/transport/filesystem.md)
    * [MongoDB](https://github.com/php-enqueue/enqueue-dev/blob/master/docs/transport/mongodb.md)
    * [WAMP](https://github.com/php-enqueue/enqueue-dev/blob/master/docs/transport/wamp.md)
    * [PHP-FPM](https://github.com/makasim/php-fpm-queue)
    * [rabbitmq-cli-consumer-client](https://github.com/makasim/rabbitmq-cli-consumer-client)
    
* Consume messages as they arrive from multiple queues. 
* You can run fewer work processes and reduce memory usages. 
* It uses long pulling whenever possible. It results in zero CPU usages while waiting for messages. 
* You can [monitor](https://github.com/php-enqueue/enqueue-dev/blob/master/docs/monitoring.md) any transport, not only redis 
* Adds extension points
* AMQP friendly. 
* Popular solution, big and active community around the project
* Supported by a company - Forma-Pro


## Resources

* [Documentation](https://github.com/php-enqueue/enqueue-dev/tree/master/docs/laravel)
* [Questions](https://gitter.im/php-enqueue/Lobby)
* [Issue Tracker](https://github.com/php-enqueue/enqueue-dev/issues)

## Developed by Forma-Pro

Forma-Pro is a full stack development company which interests also spread to open source development. 
Being a team of strong professionals we have an aim an ability to help community by developing cutting edge solutions in the areas of e-commerce, docker & microservice oriented architecture where we have accumulated a huge many-years experience. 
Our main specialization is Symfony framework based solution, but we are always looking to the technologies that allow us to do our job the best way. We are committed to creating solutions that revolutionize the way how things are developed in aspects of architecture & scalability.

If you have any questions and inquires about our open source development, this product particularly or any other matter feel free to contact at opensource@forma-pro.com

## License

It is released under the [MIT License](LICENSE).

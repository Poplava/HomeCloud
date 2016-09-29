<?php

namespace AppBundle\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class Upload implements ConsumerInterface
{
    public function execute(AMQPMessage $msg)
    {
        print_r(unserialize($msg->body));
        print_r("\n");
    }
}

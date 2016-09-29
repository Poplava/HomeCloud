<?php

namespace AppBundle\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class Upload implements ConsumerInterface
{
    public function execute(AMQPMessage $msg)
    {
        @unlink($msg->body);
        print_r($msg->body);
        print_r("\n");
    }
}

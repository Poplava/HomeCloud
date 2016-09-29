<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FooCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('foo')
            ->setDescription('Foo bar.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $msg = [
            'user_id' => 1235,
            'image_path' => '/path/to/new/pic.png'
        ];

        $this
            ->getContainer()
            ->get('old_sound_rabbit_mq.upload_picture_producer')
            ->publish(serialize($msg));

        return 0;
    }
}

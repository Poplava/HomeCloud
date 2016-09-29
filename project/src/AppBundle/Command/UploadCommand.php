<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UploadCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('app:upload')
            ->addArgument('dir', InputArgument::REQUIRED, 'The directory from where to upload files.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $sourceDir = $input->getArgument('dir');
        $tmpDir = $container->getParameter('tmp_dir');
        $producer = $container->get('old_sound_rabbit_mq.upload_producer');

        $fs = $container->get('filesystem');

        if (!$fs->exists($tmpDir)) {
            $fs->mkdir($tmpDir);
        }

        $directory = new \RecursiveDirectoryIterator($sourceDir);
        $iterator = new \RecursiveIteratorIterator($directory);

        $published = 0;

        foreach($iterator as $path => $object){
            if (!is_dir($path)) {
                $hash = md5_file($path);
                $filename = $tmpDir . DIRECTORY_SEPARATOR . $hash;
                $fs->copy($path, $filename);
                $producer->publish($filename);
                $published++;
            }
        }

        $output->writeln('Successfully uploaded: ' . $published);

        return 0;
    }
}

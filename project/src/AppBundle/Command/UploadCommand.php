<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UploadCommand extends ContainerAwareCommand
{
    const INFO_STEP = 100;

    protected function configure()
    {
        $this->setName('app:upload')
            ->addArgument('dir', InputArgument::REQUIRED, 'The directory from where to upload files.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $sourceDir = $input->getArgument('dir');
        $producer = $container->get('old_sound_rabbit_mq.upload_producer');
        $fs = $container->get('filesystem');
        $fileService = $container->get('file');

        $directory = new \RecursiveDirectoryIterator($sourceDir);
        $iterator = new \RecursiveIteratorIterator($directory);

        $published = 0;

        foreach ($iterator as $path => $object) {
            if (!is_dir($path)) {
                $hash = $fileService->getFileHash($path);
                $filename = $fileService->getTemporaryPath($hash);
                $fs->copy($path, $filename);
                $producer->publish(serialize([
                    'hash' => $hash,
                    'file' => $path,
                ]));
                $published++;

                if ($published % self::INFO_STEP == 0) {
                    $output->writeln('Uploaded: ' . $published);
                }
            }
        }

        $output->writeln('Successfully uploaded: ' . $published);

        return 0;
    }
}

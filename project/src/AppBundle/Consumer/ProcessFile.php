<?php

namespace AppBundle\Consumer;

use AppBundle\Entity\File;
use AppBundle\Service\File as FileService;
use AppBundle\Service\Identity;
use Doctrine\ORM\EntityManager;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use PHPExif\Exif;

class ProcessFile implements ConsumerInterface
{
    /**
     * @var Identity
     */
    private $identityService;

    public function __construct(Identity $fileService)
    {
        $this->identityService = $fileService;
    }

    public function execute(AMQPMessage $msg)
    {
        $msg = unserialize($msg->body);

        print "Processing: " . $msg['file'] . "\n";

        $file = $this->identityService->findOrCreateFile($msg['hash']);
        $tmpExists = $this->identityService->isTemporaryFileExists($file);

        print "Temporary file exists: " . ($tmpExists ? "yes" : "no") . "\n";

        $photo = $this->identityService->findOrCreatePhoto($file);

        if ($photo != null) {
            print "Photo detected: " . $photo->getName() . "\n";
            return;
        }

        return;
//
//        if ($this->fileService->exists($msg['hash'])) {
//            print "Duplicate.\n";
//            return;
//        }
//
//        $file = $this->fileService->create($msg['hash']);
//        $exif = $this->fileService->getExif($file);
//
//        if ($exif && $exif->getMimeType() == 'image/jpeg') {
//            print "Photo detected.\n";
//            $photo = $this->fileService->createPhoto($file, $exif);
//            $this->fileService->move($file, $photo);
//            return;
//        }
//
//        return;
    }
}
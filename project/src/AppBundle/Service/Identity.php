<?php

namespace AppBundle\Service;

use AppBundle\Entity\File;
use AppBundle\Entity\Photo;
use Doctrine\ORM\EntityManager;
use PHPExif\Exif;
use PHPExif\Reader\Reader;
use Symfony\Component\Filesystem\Filesystem;

class Identity
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var string
     */
    private $tmpDir;

    /**
     * @var string
     */
    private $storageDir;

    private $reader;

    public function __construct(EntityManager $em, Filesystem $fs, $tmpDir, $storageDir)
    {
        $this->em = $em;
        $this->fs = $fs;
        $this->tmpDir = $tmpDir;
        $this->storageDir = $storageDir;
        $this->reader = Reader::factory(Reader::TYPE_NATIVE);

        if (!$fs->exists($tmpDir)) {
            $fs->mkdir($tmpDir);
        }

        if (!$fs->exists($storageDir)) {
            $fs->mkdir($storageDir);
        }
    }

    public function getTemporaryFilename(File $file)
    {
        return $this->tmpDir . DIRECTORY_SEPARATOR . $file->getHash();
    }

    public function getPhotoFilename(Photo $photo)
    {
        return $this->storageDir . DIRECTORY_SEPARATOR . $photo->getPath();
    }

    public function findOrCreateFile($hash)
    {
        $file = $this->em->getRepository('AppBundle:File')->findOneBy([
            'hash' => $hash,
        ]);

        if ($file == null) {
            $file = new File();
            $file->setHash($hash);
            //TODO: size?
            $file->setSize(0);
            $this->em->persist($file);
            $this->em->flush();
        }

        return $file;
    }

    public function isTemporaryFileExists(File $file): bool
    {
        $filename = $this->getTemporaryFilename($file);

        return $this->fs->exists($filename);
    }

    public function findOrCreatePhoto(File $file)
    {
        $photo = $this->em->getRepository('AppBundle:Photo')->findOneBy([
            'file' => $file,
        ]);

        if ($photo != null) {
            return $photo;
        }

        $filename = $this->getTemporaryFilename($file);
        $exif = $this->reader->read($filename);
        $creationDate = $exif ? $exif->getCreationDate() : false;

        if (!$creationDate || $exif->getMimeType() != 'image/jpeg') {
            return null;
        }

        $photo = new Photo();

        $name = $creationDate->format('Y/m/d/YmdHis') . ".jpg";
        $mimeType = $exif->getMimeType();

        $photo->setName($name);
        $photo->setExifGps($exif->getGPS());
        $photo->setFile($file);
        $photo->setPath($name);
        $photo->setMimeType($mimeType);

        $photoFilename = $this->getPhotoFilename($photo);

        $this->fs->mkdir(dirname($photoFilename));
        $this->fs->rename($filename, $photoFilename);

        $this->em->persist($photo);
        $this->em->flush();

        return $photo;
    }
}
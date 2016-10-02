<?php

namespace AppBundle\Service;

use AppBundle\Entity\Photo;
use Doctrine\ORM\EntityManager;
use PHPExif\Exif;
use PHPExif\Reader\Reader;
use Symfony\Component\Filesystem\Filesystem;

class File
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var \AppBundle\Repository\FileRepository
     */
    private $fileRepo;

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
        $this->fileRepo = $em->getRepository('AppBundle:File');
        $this->fs = $fs;
        $this->tmpDir = $tmpDir;
        $this->storageDir = $storageDir;
        $this->reader = Reader::factory(Reader::TYPE_NATIVE);

        if (!$fs->exists($tmpDir)) {
            $fs->mkdir($tmpDir);
        }
    }

    public function getTemporaryPath($hash): string
    {
        return $this->tmpDir . DIRECTORY_SEPARATOR . $hash;
    }

    public function getFileHash($filename): string
    {
        return md5_file($filename);
    }

    public function exists($hash): bool
    {
        return (bool) $this->fileRepo->findOneBy(['hash' => $hash]);
    }

    public function getExif(\AppBundle\Entity\File $file)
    {
        $filename = $this->getTemporaryPath($file->getHash());
        return $this->reader->read($filename);
    }

    public function create($hash)
    {
        $filename = $this->getTemporaryPath($hash);

        $file = new \AppBundle\Entity\File();
        $file->setHash($hash);
        $file->setSize(filesize($filename));
        $this->em->persist($file);
        $this->em->flush();

        return $file;
    }

    public function createPhoto(\AppBundle\Entity\File $file, Exif $exif)
    {
        $photo = new Photo();
        $creationDate = $exif->getCreationDate();
        $path = $creationDate->format('Y/m/d/YmdHis') . ".jpg";
        $mimeType = $exif->getMimeType();

        $photo->setName($file->getId());
        $photo->setExifGps($exif->getGPS());
        $photo->setFile($file);
        $photo->setPath($path);
        $photo->setMimeType($mimeType);

        $this->em->persist($photo);
        $this->em->flush();

        return $photo;
    }

    public function move(\AppBundle\Entity\File $file, Photo $photo)
    {
        $filename = $this->getTemporaryPath($file->getHash());
        $photoPath = $this->storageDir . DIRECTORY_SEPARATOR . $photo->getPath();
        $this->fs->mkdir(dirname($photoPath));
        $this->fs->rename($filename, $photoPath);
    }
}
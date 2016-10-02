<?php

namespace AppBundle\Entity;

/**
 * Photo
 */
class Photo
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $exifGps;

    /**
     * @var File
     */
    private $file;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $mimeType;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Photo
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set exifGps
     *
     * @param string $exifGps
     *
     * @return Photo
     */
    public function setExifGps($exifGps)
    {
        $this->exifGps = $exifGps;

        return $this;
    }

    /**
     * Get exifGps
     *
     * @return string
     */
    public function getExifGps()
    {
        return $this->exifGps;
    }

    /**
     * @return File
     */
    public function getFile(): File
    {
        return $this->file;
    }

    /**
     * @param File $file
     */
    public function setFile(File $file)
    {
        $this->file = $file;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    /**
     * @param string $mimeType
     */
    public function setMimeType(string $mimeType)
    {
        $this->mimeType = $mimeType;
    }
}


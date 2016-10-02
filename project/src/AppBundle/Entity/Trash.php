<?php

namespace AppBundle\Entity;

/**
 * Trash
 */
class Trash
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var File
     */
    private $file;


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
     * Get file
     *
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }
}


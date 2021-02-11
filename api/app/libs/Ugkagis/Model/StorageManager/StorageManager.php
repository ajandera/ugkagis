<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\Model\StorageManager;

use Nette;

class StorageManager extends Nette\Object
{
    /** @var  string */
    private $dir;

    /** @var  string */
    private $folder;

    /**
     * @param string $dir
     * @param $folder
     */
    public function __construct($dir, $folder)
    {
        $this->dir = $dir;
        $this->folder = $folder;
    }

    /**
     * @param $name
     * @return string
     */
    public function getLocalStorage($name)
    {
        return $this->dir . '/dataStorage/'. $name;
    }

    /**
     * @param $src
     * @param $file
     * @return mixed|null
     */
    public function addStorage($src, $file)
    {
        $upload = $this->getLocalStorage($src);
        $file->move($upload);
        return $src;
    }
}

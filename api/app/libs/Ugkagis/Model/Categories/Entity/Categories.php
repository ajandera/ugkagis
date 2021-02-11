<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\Model\Categories\Entity;

use App\ePro\BaseEntity;
use App\ePro\Translations\Translations;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @package App\Model\Categories\Entity
 * @author Aleš Jandera
 */
class Categories extends BaseEntity
{
    /**
     * @var int
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * Translations
     * @var string
     * @ORM\Column(type="text")
     */
    protected $name;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", options={"default"=0})
     */
    protected $enabled = 0;

    /**
     * Translations
     * @var string
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @Translations
     * @param string $name
     * @param int $language
     */
    public function setName($name, $language)
    {
        $this->name = Translations::setTranslation($name, $language, $this->name);
    }
    
    /**
     * @Translations
     * @param int $language
     * @return string
     */
    public function getName($language)
    {
        return Translations::getTranslation($language, $this->name);
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }
    
    /**
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @Translations
     * @param string $description
     * @param int $language
     */
    public function setDescription($description, $language)
    {
        $this->description = Translations::setTranslation($description, $language, $this->description);
    }
    
    /**
     * @Translations
     * @param int $language
     * @return string
     */
    public function getDescription($language)
    {
        return Translations::getTranslation($language, $this->description);
    }
}

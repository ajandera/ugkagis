<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\Model\Cms\Entity;

use App\ePro\Translations\Translations;
use App\Model\Categories\Entity\Categories;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @package App\Model\Seniority\Entity
 * @author Aleš Jandera
 */
class Cms
{
    /**
     * @var integer
     * @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue
     */
    private $id;

    /**
     * Translations
     * @var string
     * @ORM\Column(type="text")
     */
    private $name;

    /**
     * Translations
     * @var string
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @var Categories
     * @ORM\OneToMany(targetEntity="App\Model\Categories\Entity\Categories", mappedBy="cms")
     * @ORM\JoinColumn(name="Categories_id", referencedColumnName="id")
     */
    private $categories;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * @Translations
     * @param string $content
     * @param int $language
     */
    public function setContent($content, $language)
    {
        $this->content = Translations::setTranslation($content, $language, $this->content);
    }
    
    /**
     * @Translations
     * @param int $language
     * @return string
     */
    public function getContent($language)
    {
        return Translations::getTranslation($language, $this->content);
    }

    /**
     * @return Categories
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param Categories $categories
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }
}

<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\Model\Language\Entity;

use App\ePro\BaseEntity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @package App\Model\Entities
 * @author Aleš Jandera
 */
class Language extends BaseEntity
{
    /**
     * @var integer
     * @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue
     */
    private $id;

    /**
     * Translations
     * @var string
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $code;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $translationCode;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $defaults;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $visible;

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
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param int $default
     */
    public function setDefaults($default)
    {
        $this->defaults = $default;
    }

    /**
     * @return int
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * @param int $visible
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
    }

    /**
     * @return int
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * @param string $translationCode
     */
    public function setTranslationCode($translationCode)
    {
        $this->translationCode = $translationCode;
    }

    /**
     * @return string
     */
    public function getTranslationCode()
    {
        return $this->translationCode;
    }
}

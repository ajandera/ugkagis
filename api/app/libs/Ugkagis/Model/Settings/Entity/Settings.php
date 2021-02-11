<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\Model\Settings\Entity;

use App\ePro\BaseEntity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @package App\Model\Entities
 * @author Aleš Jandera
 */
class Settings extends BaseEntity
{
    /**
     * @var integer
     * @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $keyString;

    /**
     * @var string
     * @ORM\Column(type="string",length=4096, nullable=true)
     */
    private $value;

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
     * @param string $key
     */
    public function setKey($key)
    {
        $this->keyString = $key;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->keyString;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
 }

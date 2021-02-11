<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\Model\Users\Entity;

use App\ePro\BaseEntity;
use App\ePro\Translations\Translations;
use Nette\DateTime;
use Nette\Security\Passwords;
use App\Model\Documents\Entity\Documents;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @package App\Model\Users\Entity
 * @author Aleš Jandera
 */
class Users extends BaseEntity
{
    const ADMIN = 0;
    const EMPLOYEE = 1;

    /**
     * @var integer
     * @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $username;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     */
    private $role;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $restorePasswordToken;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $tokenValidDate;
    
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $name;
    
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $surname;

    /**
     * @var integer
     * @ORM\Column(type="bigint", nullable=true)
     */
    private $phone;

    /**
     * @var Documents[]
     * @ORM\ManyToMany(targetEntity="App\Model\Documents\Entity\Documents")
     * @ORM\JoinColumn(name="document_id", referencedColumnName="id")
     */
    private $documents;

    /**
     * Translations
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $position;

    /**
     * Translations
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;
    
    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $enabled = true;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $dgdelete = false;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $picture;

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
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = Passwords::hash($password);
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param int $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @return int
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param string $restorePasswordToken
     */
    public function setRestorePasswordToken($restorePasswordToken)
    {
        $this->restorePasswordToken = $restorePasswordToken;
    }

    /**
     * @return string
     */
    public function getRestorePasswordToken()
    {
        return $this->restorePasswordToken;
    }

    /**
     * Set token validation for reset password.
     */
    public function setTokenValidDate()
    {
        $this->tokenValidDate = new DateTime('+ 2 hours');
    }

    /**
     * @return bool
     */
    public function isTokenValidDate()
    {
        $date = Time() - strtotime($this->tokenValidDate->format('Y-m-d H:i'));
        return ($date < 0) ? true : false;
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
     * @param string $surname
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    /**
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @param int $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return int
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->name . ' ' . $this->surname;
    }

    /**
     * @param Documents[] $documents
     */
    public function setDocuments($documents)
    {
        $this->documents = $documents;
    }

    /**
     * @return Documents[]
     */
    public function getDocuments()
    {
        return $this->documents;
    }
    
    /**
     * @param boolean $enabled
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
     * @param $delete
     */
    public function setDelete($delete)
    {
        $this->dgdelete = $delete;
    }

    /**
     * @return bool
     */
    public function getDelete()
    {
        return $this->dgdelete;
    }

    /**
     * @param int $language
     * @return string
     */
    public function getDescription($language)
    {
        return Translations::getTranslation($language, $this->description);
    }

    /**
     * @param string $description
     * @param int $language
     */
    public function setDescription($description, $language)
    {
        $this->description = Translations::setTranslation($description, $language, $this->description);
    }

    /**
     * @param string $position
     * @param $language
     */
    public function setPosition($position, $language)
    {
        $this->position = Translations::setTranslation($position, $language, $this->position);
    }

    /**
     * @param $language
     * @return string
     */
    public function getPosition($language)
    {
        return Translations::getTranslation($language, $this->position);
    }

    /**
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * @param string $picture
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;
    }
}

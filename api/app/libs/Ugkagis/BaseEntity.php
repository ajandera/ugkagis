<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\ePro;

use App\Model\Users\Entity\Users;
use Doctrine\ORM\Mapping AS ORM;
use Nette\Utils\DateTime;

/**
 * @ORM\MappedSuperclass
 * @package App\ePro
 * @author Aleš Jandera
 */
class BaseEntity
{
    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    protected $created;
    
    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    protected $modified;

    /**
     * @var Users
     * @ORM\ManyToOne(targetEntity="App\Model\Users\Entity\Users")
     * @ORM\JoinColumn(name="users_id", referencedColumnName="id", nullable=true)
     */
    protected $users;

    /**
     * @var Users
     * @ORM\ManyToOne(targetEntity="App\Model\Users\Entity\Users")
     * @ORM\JoinColumn(name="users_modified_id", referencedColumnName="id", nullable=true)
     */
    protected $usersModified;

    /**
     * Set created time.
     * @param null
     */
    public function setCreated()
    {
        if ($this->created == null) {
            $this->created = new DateTime();
        }
    }

    /**
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set modified time.
     * @param null
     */
    public function setModified()
    {
        $this->modified = new DateTime();
    }

    /**
     * @return DateTime
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * @param Users $users
     */
    public function setUsers($users)
    {
        $this->users = $users;
    }

    /**
     * @return Users
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param Users $usersModified
     */
    public function setUsersModified($usersModified)
    {
        $this->usersModified = $usersModified;
    }

    /**
     * @return Users
     */
    public function getUsersModified()
    {
        return $this->usersModified;
    }
}

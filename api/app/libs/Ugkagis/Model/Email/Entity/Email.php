<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\Model\Email\Entity;

use App\Model\Users\Entity\Users;
use Doctrine\ORM\Mapping AS ORM;
use Nette\Utils\DateTime;

/**
 * @ORM\Entity
 * @package App\Model\Email\Entity
 * @author Aleš Jandera
 */
class Email
{
    /**
     * @var integer
     * @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue
     */
    private $id;
    
    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $subject;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $body;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     */
    private $priority;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     */
    private $send;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $bcc;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $attachment;

    /**
     * @var Users
     * @ORM\ManyToOne(targetEntity="App\Model\Users\Entity\Users")
     * @ORM\JoinColumn(name="users_id", referencedColumnName="id", nullable=true)
     */
    protected $users;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $error;

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
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = json_encode($email);
    }

    /**
     * @return array
     */
    public function getEmail()
    {
        return json_decode($this->email);
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param int $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param int $send
     */
    public function setSend($send)
    {
        $this->send = $send;
    }

    /**
     * @return int
     */
    public function getSend()
    {
        return $this->send;
    }
    
    /**
     * Set created date.
     */
    public function setCreated()
    {
        $this->created = new DateTime();
    }
    
    /**
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param array $bcc
     */
    public function setBcc($bcc)
    {
        $this->bcc = json_encode($bcc);
    }

    /**
     * @return array
     */
    public function getBcc()
    {
        return json_decode($this->bcc);
    }

    /**
     * @param array $attachment
     */
    public function setAttachment($attachment)
    {
        $this->attachment = json_encode($attachment);
    }

    /**
     * @return array
     */
    public function getAttachment()
    {
        return json_decode($this->attachment);
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
     * @param string $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }
}

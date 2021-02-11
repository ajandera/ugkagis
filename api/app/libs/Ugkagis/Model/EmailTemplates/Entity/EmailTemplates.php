<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\Model\EmailTemplate\Entity;

use App\ePro\BaseEntity;
use App\ePro\Translations\Translations;
use Doctrine\ORM\Mapping AS ORM;
use App\Model\Documents\Entity\Documents;

/**
 * @ORM\Entity
 * @package App\Model\EmailTemplate\Entity
 * @author Aleš Jandera
 */
class EmailTemplates extends BaseEntity
{
    const NO_EVENT = 0;
    const FORGOT = 1;
    const REGISTER_CONSULTANT = 2;
    const REGISTER_PARTNER = 3;
    const NOTIFY_USER_AVAILABLE = 4;
    const NOTIFY_WORK_AVAILABLE = 5;
    const NEWSLETTER = 8;
    const REACTION = 9;

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
     * @ORM\Column(type="text")
     */
    private $subject;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $body;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $locked = 0;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $type;

    /**
     * @var array
     * @ORM\Column(type="text", nullable=true)
     */
    private $workflow;

    /**
     * @var Documents[]
     * @ORM\ManyToMany(targetEntity="App\Model\Documents\Entity\Documents")
     * @ORM\JoinColumn(name="document_id", referencedColumnName="id")
     */
    private $documents;

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
     * @Translations
     * @param string $subject
     * @param int $language
     */
    public function setSubject($subject, $language)
    {
        $this->subject = Translations::setTranslation($subject, $language, $this->subject);
    }
    
    /**
     * @Translations
     * @param int $language
     * @return string
     */
    public function getSubject($language)
    {
        return Translations::getTranslation($language, $this->subject);
    }

    /**
     * @Translations
     * @param string $body
     * @param int $language
     */
    public function setBody($body, $language)
    {
        $this->body = Translations::setTranslation($body, $language, $this->body);
    }
    
    /**
     * @Translations
     * @param int $language
     * @return string
     */
    public function getBody($language)
    {
        return Translations::getTranslation($language, $this->body);
    }

    /**
     * Set locked.
     * @param int $locked
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;
    }

    /**
     * @return int
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * Set type.
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param array $workflow
     */
    public function setWorkflow($workflow)
    {
        $this->workflow = json_encode($workflow);
    }

    /**
     * @return array
     */
    public function getWorkflow()
    {
        return json_decode($this->workflow);
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
}

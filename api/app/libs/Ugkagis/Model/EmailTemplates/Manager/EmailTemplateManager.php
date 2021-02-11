<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\Model\EmailTemplate\Manager;

use App\Model\EmailTemplate\Storage\IEmailTemplatesDao;
use App\Model\EmailTemplate\Entity\EmailTemplates;
use App\Model\Language\Entity\Language;

/**
 * Class EmailTemplateManager
 * @package App\Model\EmailTemplate\Manager
 */
class EmailTemplateManager
{
    /** @var IEmailTemplatesDao */
    private $emailTemplateDao;
    
    public function __construct(
        IEmailTemplatesDao $emailTemplateDao
    )
    {
        $this->emailTemplateDao = $emailTemplateDao;
    }
    
    /**
     * Get all email templates.
     * @return EmailTemplates[]
     */
    public function getEmailTemplates()
    {
        return $this->emailTemplateDao->findAll();
    }

    /**
     * Get all email templates by conditions.
     * @param $conditions
     * @return EmailTemplates[]
     */
    public function getEmailTemplatesBy($conditions)
    {
        return $this->emailTemplateDao->findBy($conditions);
    }

    /**
     * Get all email template by conditions.
     * @param $conditions
     * @return EmailTemplates
     */
    public function getEmailTemplateBy($conditions)
    {
        return $this->emailTemplateDao->findOneBy($conditions);
    }

    /**
     * Get email template by id.
     * @param int $emailTemplatesId
     * @return EmailTemplates
     */
    public function getEmailTemplateById($emailTemplatesId)
    {
        return $this->emailTemplateDao->findById($emailTemplatesId);
    }

    /**
     * Delete email template by id.
     * @param int $id
     */
    public function deleteEmailTemplate($id)
    {
        $emailTemplate = $this->getEmailTemplateById($id);
        $this->emailTemplateDao->delete($emailTemplate);
    }

    /**
     * Persist email template to storage
     * @param string $name
     * @param string $subject
     * @param string $body
     * @param Language $language
     * @param $user
     * @param $type
     */
    public function addEmailTemplate($name, $subject, $body, $language, $user, $type)
    {
        $emailTemplate = new EmailTemplates();
        $emailTemplate->setName($name);
        $emailTemplate->setSubject($subject, $language->getId());
        $emailTemplate->setBody($body, $language->getId());
        $emailTemplate->setCreated();
        $emailTemplate->setModified();
        $emailTemplate->setUsers($user);
        $emailTemplate->setUsersModified($user);
        $emailTemplate->setType($type);
        $this->emailTemplateDao->save($emailTemplate);
    }

    /**
     * Update email template.
     * @param int $id
     * @param string $name
     * @param string $subject
     * @param string $body
     * @param Language $language
     * @param $user
     * @param $type
     */
    public function updateEmailTemplate($id, $name, $subject, $body, $language, $user, $type)
    {
        $emailTemplate = $this->getEmailTemplateById($id);
        $emailTemplate->setName($name);
        $emailTemplate->setSubject($subject, $language->getId());
        $emailTemplate->setBody($body, $language->getId());
        $emailTemplate->setModified();
        $emailTemplate->setUsersModified($user);
        $emailTemplate->setType($type);
        $this->emailTemplateDao->save($emailTemplate);
    }
}

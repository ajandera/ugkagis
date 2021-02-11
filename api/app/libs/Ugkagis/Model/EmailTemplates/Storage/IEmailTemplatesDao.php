<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\Model\EmailTemplate\Storage;

use App\Model\EmailTemplate\Entity\EmailTemplates;

/**
 * Interface IEmailTemplatesDao
 * @package App\Model\EmailTemplate\Storage
 */
interface IEmailTemplatesDao
{
    /**
     * Persist emailTemplates to storage.
     * @param EmailTemplates $emailTemplates
     */
    public function save(EmailTemplates $emailTemplates);

    /**
     * Delete emailTemplates from storage.
     * @param EmailTemplates $emailTemplates
     */
    public function delete(EmailTemplates $emailTemplates);

    /**
     * Find all emailTemplates
     * @return EmailTemplates[]
     */
    public function findAll();
    
    /**
     * Find all emailTemplates by parameters
     * @param array $conditions
     * @return EmailTemplates[]
     */
    public function findBy($conditions);

    /**
     * Find emailTemplates by parameters
     * @param array $conditions
     * @return EmailTemplates
     */
    public function findOneBy($conditions);

    /**
     * Find emailTemplates by id
     * @param int $emailTemplatesId
     * @return EmailTemplates
     */
    public function findById($emailTemplatesId);

    /**
     * Returns count of all emailTemplates.
     * @param array $conditions
     * @return int
     */
    public function countOfAll($conditions = []);
}

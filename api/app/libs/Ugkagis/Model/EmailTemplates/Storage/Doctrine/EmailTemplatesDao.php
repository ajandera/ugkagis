<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\Model\EmailTemplate\Storage\Doctrine;

use App\Model\EmailTemplate\Entity\EmailTemplates;
use App\Model\EmailTemplate\Storage\IEmailTemplatesDao;
use Doctrine\ORM\Mapping;
use Kdyby\Doctrine\EntityDao AS DoctrineEntityDao;

/**
 * Class EmailTemplatesDao
 * @package App\Model\EmailTemplate\Storage\Doctrine
 */
class EmailTemplatesDao implements IEmailTemplatesDao
{
    /** @var DoctrineEntityDao */
    private $emailTemplatesDao;

    public function __construct(DoctrineEntityDao $emailTemplatesDao)
    {
        $this->emailTemplatesDao = $emailTemplatesDao;
    }

    /**
     * Persist emailTemplates to storage.
     * @param EmailTemplates $emailTemplates
     */
    public function save(EmailTemplates $emailTemplates)
    {
        $this->emailTemplatesDao->getEntityManager()->persist($emailTemplates);
        $this->emailTemplatesDao->getEntityManager()->flush();
    }

    /**
     * Delete EmailTemplates from storage.
     * @param EmailTemplates $emailTemplates
     */
    public function delete(EmailTemplates $emailTemplates)
    {
        $this->emailTemplatesDao->getEntityManager()->remove($emailTemplates);
        $this->emailTemplatesDao->getEntityManager()->flush();
    }

    /**
     * Find all emailTemplates.
     * @return EmailTemplates[]
     */
    public function findAll()
    {
        return $this->emailTemplatesDao->findAll();
    }
    
    /**
     * Find all emailTemplates by parameters
     * @param array $conditions
     * @return EmailTemplates[]
     */
    public function findBy($conditions)
    {
        return $this->emailTemplatesDao->findBy($conditions);
    }

    /**
     * Find emailTemplates by parameters
     * @param array $conditions
     * @return EmailTemplates
     */
    public function findOneBy($conditions)
    {
        return $this->emailTemplatesDao->findOneBy($conditions);
    }

    /**
     * Find emailTemplates by id
     * @param int $emailTemplatesId
     * @return EmailTemplates
     */
    public function findById($emailTemplatesId)
    {
        return $this->emailTemplatesDao->find($emailTemplatesId);
    }

    /**
     * Returns count of all emailTemplates.
     * @param array $conditions
     * @return int
     */
    public function countOfAll($conditions = [])
    {
        $qb = $this->emailTemplatesDao->createQueryBuilder('t');
        $qb->select('count(t.id)');
        $qb->whereCriteria($conditions);

        return $qb->getQuery()->getSingleScalarResult();
    }
}

<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\Model\Email\Storage\Doctrine;

use App\Model\Email\Entity\Email;
use App\Model\Email\Storage\IEmailDao;
use Doctrine\ORM\Mapping;
use Kdyby\Doctrine\EntityDao AS DoctrineEntityDao;

/**
 * Class EmailDao
 * @package App\Model\Email\Storage\Doctrine
 */
class EmailDao implements IEmailDao
{
    /** @var DoctrineEntityDao */
    private $emailDao;

    public function __construct(DoctrineEntityDao $emailDao)
    {
        $this->emailDao = $emailDao;
    }

    /**
     * Persist Email to storage.
     * @param Email $email
     */
    public function save(Email $email)
    {
        $this->emailDao->getEntityManager()->persist($email);
        $this->emailDao->getEntityManager()->flush();
    }

    /**
     * Delete Email from storage.
     * @param Email $email
     */
    public function delete(Email $email)
    {
        $this->emailDao->getEntityManager()->remove($email);
        $this->emailDao->getEntityManager()->flush();
    }

    /**
     * Find all Email.
     * @return Email[]
     */
    public function findAll()
    {
        return $this->emailDao->findAll();
    }

    /**
     * Find Email by condition.
     * @param array $condition
     * @param $order
     * @param $limit
     * @param $offset
     * @return Email[]
     */
    public function findBy($condition, $order, $limit, $offset)
    {
        return $this->emailDao->findBy($condition, $order, $limit, $offset);
    }
    
    /**
     * Find Email by condition.
     * @param array $condition
     * @return Email
     */
    public function findOneBy($condition)
    {
        return $this->emailDao->findOneBy($condition);
    }

    /**
     * Find Email by id
     * @param int $emailId
     * @return object
     */
    public function findById($emailId)
    {
        return $this->emailDao->find($emailId);
    }

    /**
     * Returns count of all Email.
     * @param array $conditions
     * @return int
     */
    public function countOfAll($conditions = [])
    {
        $qb = $this->emailDao->createQueryBuilder('t');
        $qb->select('count(t.id)');
        $qb->whereCriteria($conditions);

        return $qb->getQuery()->getSingleScalarResult();
    }
}

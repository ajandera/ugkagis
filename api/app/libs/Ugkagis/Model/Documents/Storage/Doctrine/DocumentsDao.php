<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\Model\Documents\Storage\Doctrine;

use App\Model\Documents\Entity\Documents;
use App\Model\Storage\IDocumentsDao;
use Doctrine\ORM\Mapping;
use Kdyby\Doctrine\EntityDao AS DoctrineEntityDao;

class DocumentsDao implements IDocumentsDao
{
    /** @var DoctrineEntityDao */
    private $documentsDao;

    public function __construct(DoctrineEntityDao $documentsDao)
    {
        $this->documentsDao = $documentsDao;
    }

    /**
     * Persist documents to storage.
     * @param Documents $documents
     */
    public function save(Documents $documents)
    {
        $this->documentsDao->getEntityManager()->persist($documents);
        $this->documentsDao->getEntityManager()->flush();
    }

    /**
     * Delete Documents from storage.
     * @param Documents $documents
     */
    public function delete(Documents $documents)
    {
        $this->documentsDao->getEntityManager()->remove($documents);
        $this->documentsDao->getEntityManager()->flush();
    }

    /**
     * Find all documents.
     * @return Documents[]
     */
    public function findAll()
    {
        return $this->documentsDao->findAll();
    }
    
    /**
     * Find all documents by parameters
     * @param array $conditions
     * @return Documents[]
     */
    public function findBy($conditions)
    {
        return $this->documentsDao->findBy($conditions);
    }

    /**
     * Find documents by id
     * @param int $documentsId
     * @return Documents
     */
    public function findById($documentsId)
    {
        return $this->documentsDao->find($documentsId);
    }

    /**
     * Returns count of all documents.
     * @param array $conditions
     * @return int
     */
    public function countOfAll($conditions = [])
    {
        $qb = $this->documentsDao->createQueryBuilder('t');
        $qb->select('count(t.id)');
        $qb->whereCriteria($conditions);

        return $qb->getQuery()->getSingleScalarResult();
    }
}

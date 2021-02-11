<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\Model\Cms\Storage\Doctrine;

use App\Model\Cms\Entity\Cms;
use App\Model\Cms\Storage\ICmsDao;
use Doctrine\ORM\Mapping;
use Kdyby\Doctrine\EntityDao AS DoctrineEntityDao;

/**
 * Class CmsDao
 * @package App\Model\Cms\Storage\Doctrine
 */
class CmsDao implements ICmsDao
{
    /** @var DoctrineEntityDao */
    private $cmsDao;

    public function __construct(DoctrineEntityDao $cmsDao)
    {
        $this->cmsDao = $cmsDao;
    }

    /**
     * Persist cms to storage.
     * @param Cms $cms
     */
    public function save(Cms $cms)
    {
        $this->cmsDao->getEntityManager()->persist($cms);
        $this->cmsDao->getEntityManager()->flush();
    }

    /**
     * Delete Cms from storage.
     * @param Cms $cms
     */
    public function delete(Cms $cms)
    {
        $this->cmsDao->getEntityManager()->remove($cms);
        $this->cmsDao->getEntityManager()->flush();
    }

    /**
     * Find all cms .
     * @return Cms[]
     */
    public function findAll()
    {
        return $this->cmsDao->findAll();
    }

    /**
     * Find all cms by condition.
     * @param array $condition
     * @return Cms[]
     */
    public function findBy($condition = [])
    {
        return $this->cmsDao->findBy($condition);
    }

    /**
     * Find cms by id
     * @param int $cmsId
     * @return Cms|object
     */
    public function findById($cmsId)
    {
        return $this->cmsDao->find($cmsId);
    }

    /**
     * Returns count of all cms.
     * @param array $conditions
     * @return int
     */
    public function countOfAll($conditions = [])
    {
        $qb = $this->cmsDao->createQueryBuilder('t');
        $qb->select('count(t.id)');
        $qb->whereCriteria($conditions);

        return $qb->getQuery()->getSingleScalarResult();
    }
}

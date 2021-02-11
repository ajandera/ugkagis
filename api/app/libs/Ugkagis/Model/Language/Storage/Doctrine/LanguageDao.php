<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\Model\Language\Storage\Doctrine;

use App\Model\Language\Entity\Language;
use App\Model\Language\Storage\ILanguageDao;
use Doctrine\ORM\Mapping;
use Kdyby\Doctrine\EntityDao AS DoctrineEntityDao;

/**
 * Class LanguageDao
 * @package App\Model\Language\Storage\Doctrine
 */
class LanguageDao implements ILanguageDao
{
    /** @var DoctrineEntityDao */
    private $languageDao;

    public function __construct(DoctrineEntityDao $languageDao)
    {
        $this->languageDao = $languageDao;
    }

    /**
     * Persist language to storage.
     * @param Language $language
     */
    public function save(Language $language)
    {
        $this->languageDao->getEntityManager()->persist($language);
        $this->languageDao->getEntityManager()->flush();
    }

    /**
     * Delete Language from storage.
     * @param Language $language
     */
    public function delete(Language $language)
    {
        $this->languageDao->getEntityManager()->remove($language);
        $this->languageDao->getEntityManager()->flush();
    }

    /**
     * Find all language by parameters
     * @param int $offset
     * @param int $limit
     * @param array $conditions
     * @param array $order
     * @return Language[]
     */
    public function findBy($conditions = [], $order = [], $offset = null, $limit = null )
    {
        return $this->languageDao->findBy($conditions, $order, $limit, $offset);
    }

    /**
     * Find user by id
     * @param int $userId
     * @return Language
     */
    public function findById($userId)
    {
        return $this->languageDao->find($userId);
    }

    /**
     * Returns count of all language.
     * @param array $conditions
     * @return int
     */
    public function countOfAll($conditions = [])
    {
        $qb = $this->languageDao->createQueryBuilder('t');
        $qb->select('count(t.id)');
        $qb->whereCriteria($conditions);

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param array $conditions
     * @return Language
     */
    public function findOneBy($conditions= [])
    {
        return $this->languageDao->findOneBy($conditions);
    }
}

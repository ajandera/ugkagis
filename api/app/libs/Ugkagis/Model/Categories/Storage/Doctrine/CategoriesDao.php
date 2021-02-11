<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\Model\Categories\Storage\Doctrine;

use App\Model\Categories\Entity\Categories;
use App\Model\Categories\Storage\ICategoriesDao;
use App\Model\Language\Entity\Language;
use Doctrine\ORM\Mapping;
use Kdyby\Doctrine\EntityDao AS DoctrineEntityDao;
use PDO;

/**
 * Class CategoriesDao
 * @package App\Model\Categories\Storage\Doctrine
 */
class CategoriesDao implements ICategoriesDao
{
    /** @var DoctrineEntityDao */
    private $productCategoriesDao;

    public function __construct(DoctrineEntityDao $productCategoriesDao)
    {
        $this->productCategoriesDao = $productCategoriesDao;
    }

    /**
     * Persist productCategories to storage.
     * @param Categories $productCategories
     * @throws \Exception
     */
    public function save(Categories $productCategories)
    {
        $this->productCategoriesDao->getEntityManager()->persist($productCategories);
        $this->productCategoriesDao->getEntityManager()->flush();
    }

    /**
     * Delete Categories from storage.
     * @param Categories $productCategories
     * @throws \Exception
     */
    public function delete(Categories $productCategories)
    {
        $this->productCategoriesDao->getEntityManager()->remove($productCategories);
        $this->productCategoriesDao->getEntityManager()->flush();
    }

    /**
     * Find all productCategories by conditions
     * @param array $conditions
     * @param array $order
     * @return Categories[]
     */
    public function findBy($conditions = [], $order = [])
    {
        return $this->productCategoriesDao->findBy($conditions, $order);
    }

    /**
     * Find all productCategories by conditions
     * @param array $conditions
     * @param array $order
     * @return Categories
     */
    public function findOneBy($conditions = [], $order = [])
    {
        return $this->productCategoriesDao->findOneBy($conditions, $order);
    }
    
    /**
     * Find all productCategories.
     * @return Categories[]
     */
    public function findAll()
    {
        return $this->productCategoriesDao->findAll();
    }

    /**
     * Find productCategories by id
     * @param int $productCategoriesId
     * @return Categories|object
     */
    public function findById($productCategoriesId)
    {
        return $this->productCategoriesDao->find($productCategoriesId);
    }

    /**
     * Get categories by name.
     * @param string $name
     * @param int $languageId
     * @return Categories[] ;
     */
    public function searchByName($name, $languageId)
    {
        $selectName = "TRIM(BOTH '\"' FROM c.name -> '$.\"{$languageId}\"')";
        $whereName = "LOWER(c.name -> '$.\"{$languageId}\"')";
        $sql = "SELECT c.id, {$selectName} AS name, c.lft, c.rgt, c.lvl, (SELECT count(id) FROM categories WHERE parent_id = c.id) AS hasChildren
FROM categories AS c
WHERE {$whereName} LIKE :name AND root = 0";
        $params['name'] = '%' . strtolower($name) . '%';
        $search = $this->productCategoriesDao->getEntityManager()->getConnection()->prepare($sql);
        $search->execute($params);
        return $search->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns count of all productCategories.
     * @param array $conditions
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countOfAll($conditions = [])
    {
        $qb = $this->productCategoriesDao->createQueryBuilder('t');
        $qb->select('count(t.id)');
        $qb->whereCriteria($conditions);

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Get query builder.
     * @param string $index
     * @return \Kdyby\Doctrine\QueryBuilder
     */
    public function createQueryBuilder($index)
    {
        return $this->productCategoriesDao->createQueryBuilder($index);
    }

    /**
     * Get entity manager.
     */
    public function getEntityManager()
    {
        return $this->productCategoriesDao->getEntityManager();
    }
}

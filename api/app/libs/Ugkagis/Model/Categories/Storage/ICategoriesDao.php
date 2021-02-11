<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\Model\Categories\Storage;

use App\Model\Categories\Entity\Categories;

/**
 * Interface ICategoriesDao
 * @package App\Model\Categories\Storage
 */
interface ICategoriesDao
{
    /**
     * Persist productCategories to storage.
     * @param Categories $categories
     */
    public function save(Categories $categories);

    /**
     * Delete productCategories from storage.
     * @param Categories $categories
     */
    public function delete(Categories $categories);

    /**
     * Find productCategories by conditions
     * @param array $conditions
     * @param array $order
     * @return Categories[]
     */
    public function findBy($conditions = [], $order = []);

    /**
     * Find productCategories by conditions
     * @param array $conditions
     * @param array $order
     * @return Categories
     */
    public function findOneBy($conditions = [], $order = []);
    
    /**
     * Find all productCategories.
     * @return Categories[]
     */
    public function findAll();

    /**
     * Find productCategories by id
     * @param int $categoriesId
     * @return Categories
     */
    public function findById($categoriesId);

    /**
     * Get products by name.
     * @param string $name
     * @param int $languageId
     * @return Categories[]
     */
    public function searchByName($name, $languageId);

    /**
     * Returns count of all productCategories.
     * @param array $conditions
     * @return int
     */
    public function countOfAll($conditions = []);

    /**
     * Get query builder.
     * @param string $index
     * @return \Kdyby\Doctrine\QueryBuilder
     */
    public function createQueryBuilder($index);

    /**
     * Get entity manager.
     */
    public function getEntityManager();
}

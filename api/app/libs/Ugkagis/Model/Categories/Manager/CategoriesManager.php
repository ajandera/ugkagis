<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\Model\Categories\Manager;

use App\Model\Categories\Entity\Categories;
use App\Model\Categories\Storage\ICategoriesDao;
use App\Model\Language\Entity\Language;
use App\Model\Users\Entity\Users;
use App\Model\Users\Manager\UserManager;

/**
 * Class CategoriesManager
 * @package App\Model\Categories\Manager
 */
class CategoriesManager
{
    /** @var ICategoriesDao */
    private $categoryDao;

    /** @var  UserManager */
    private $userManager;
    
    public function __construct(
        ICategoriesDao $categoryDao,
        UserManager $userManager
    )
    {
        $this->categoryDao = $categoryDao;
        $this->userManager = $userManager;
    }

    /**
     * Get categories.
     * @param array $conditions
     * @param array $order
     * @return Categories[]
     */
    public function getCategories($conditions, $order)
    {
        return $this->categoryDao->findBy($conditions, $order);
    }

    /**
     * Get  category by id.
     * @param int $CategoriesId
     * @return Categories
     */
    public function getCategoryById($CategoriesId)
    {
        return $this->categoryDao->findById($CategoriesId);
    }

    /**
     * @return Categories[]
     */
    public function getAllCategories()
    {
        return $this->categoryDao->findAll();
    }

    /**
     * @param $condition
     * @return Categories[]
     */
    public function getCategoryBy($condition)
    {
        return $this->categoryDao->findBy($condition);
    }

    /**
     * @param $condition
     * @return Categories
     */
    public function getOneCategoryBy($condition)
    {
        return $this->categoryDao->findOneBy($condition);
    }

    /**
     * @param string $name
     * @param string $description
     * @param Language $language
     * @param Users $user
     * @return Categories
     */
    public function addCategory($name, $description, $language, $user)
    {
        $category = new Categories();
        $category->setEnabled(true);
        $category->setName($name, $language->getId());
        $category->setDescription($description, $language->getId());
        $category->setCreated();
        $category->setModified();
        $category->setUsers($user);
        $category->setUsersModified($user);
        $this->categoryDao->save($category);

        return $category;
    }

    /**
     * Update category.
     * @param int $id
     * @param string $name
     * @param string $description
     * @param Language $language
     * @param $user
     * @return Categories
     */
    public function updateCategory($id, $name, $description, $language, $user)
    {
        $category = $this->getCategoryById($id);
        $category->setName($name, $language->getId());
        $category->setDescription($description, $language->getId());
        $category->setModified();
        $category->setUsersModified($user);
        
        $this->categoryDao->save($category);

        return $category;
    }

    /**
     * Switch category visibility.
     * @param $Id
     */
    public function switchCategoryVisibility($Id)
    {
        $category = $this->getCategoryById($Id);
        if($category->getEnabled() == 1) {
            $category->setEnabled(0);
        } else {
            $category->setEnabled(1);
        }
        $this->categoryDao->save($category);
    }
    
    /**
     * Delete  category.
     * @param int $id
     */
    public function deleteCategory($id)
    {
        $category = $this->getCategoryById($id);
        $this->categoryDao->delete($category);
    }

    /**
     * Get  by name.
     * @param $text
     * @param int $languageId
     * @return Categories[]
     */
    public function getCategoryByName($text, $languageId)
    {
        return $this->categoryDao->searchByName($text, $languageId);
    }
}

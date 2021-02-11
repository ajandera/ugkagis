<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\Model\Cms\Manager;

use App\Model\Categories\Manager\CategoriesManager;
use App\Model\Language\Entity\Language;
use App\Model\Cms\Entity\Cms;
use App\Model\Cms\Storage\ICmsDao;

/**
 * Class CmsManager
 * @package App\Model\Cms\Manager
 */
class CmsManager
{
    /** @var ICmsDao */
    private $cmsDao;

    /** @var CategoriesManager */
    private $categoryManager;
    
    public function __construct(
        ICmsDao $cmsDao,
        CategoriesManager $categoriesManager
    ) {
        $this->cmsDao = $cmsDao;
        $this->categoryManager = $categoriesManager;
    }

    /**
     * Gt all unit.
     * @return Cms[]
     */
    public function getCms()
    {
        return $this->cmsDao->findAll();
    }

    /**
     * Gt all unit by condition.
     * @param $condition
     * @return Cms[]
     */
    public function getCmsBy($condition)
    {
        return $this->cmsDao->findBy($condition);
    }

    /**
     * Get unit by id.
     * @param $id
     * @return Cms
     */
    public function getCmsById($id)
    {
        return $this->cmsDao->findById($id);
    }

    /**
     * Delete cms.
     * @param int $id
     */
    public function deleteCms($id)
    {
        $unit = $this->getCmsById($id);
        $this->cmsDao->delete($unit);
    }

    /**
     * Add cms.
     * @param string $name
     * @param string $description
     * @param Language $language
     * @param $categoryId
     */
    public function addCms($name, $description, $language, $categoryId)
    {
        $cms = new Cms();
        $cms->setName($name, $language->getId());
        $cms->setContent($description, $language->getId());
        $cms->setCategories($this->categoryManager->getCategoryById($categoryId));
        $this->cmsDao->save($cms);
    }

    /**
     * Update cms.
     * @param int $id
     * @param string $name
     * @param string $description
     * @param Language $language
     * @param $categoryId
     */
    public function updateCms($id, $name, $description, $language, $categoryId)
    {
        $cms = $this->getCmsById($id);
        $cms->setName($name, $language->getId());
        $cms->setContent($description, $language->getId());
        $cms->setCategories($this->categoryManager->getCategoryById($categoryId));
        $this->cmsDao->save($cms);
    }
}

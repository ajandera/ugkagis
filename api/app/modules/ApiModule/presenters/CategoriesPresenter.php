<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\ApiModule;

use App\Model\Categories\Manager\CategoriesManager;
use App\Model\Language\Manager\LanguageManager;
use App\Model\Users\Manager\UserManager;
use Drahak\Restful\Application\UI\SecuredResourcePresenter;
use Nette\Neon\Exception;

/**
 * Class CategoriesPresenter
 * @package App\ApiModule
 */
class CategoriesPresenter extends SecuredResourcePresenter
{
    /** @var CategoriesManager */
    private $categoriesManager;

    /** @var LanguageManager */
    private $languageManager;

    /** @var UserManager */
    private $userManager;

    /**
     * CategoriesPresenter constructor.
     * @param CategoriesManager $categoriesManager
     * @param LanguageManager $languageManager
     * @param UserManager $userManager
     */
    public function __construct(
        CategoriesManager $categoriesManager,
        LanguageManager $languageManager,
        UserManager $userManager
    )
    {
        parent::__construct();
        $this->categoriesManager = $categoriesManager;
        $this->languageManager = $languageManager;
        $this->userManager = $userManager;
    }

    /**
     * @GET categories
     * @param int $id
     */
    public function actionCategories($id)
    {
        $categories = [];
        $language = $this->languageManager->getDefaultLanguage();
        foreach ($this->categoriesManager->getCategories([], []) as $item) {
            $categories[] = [
                'id' => $item->getId(),
                'name' => $item->getName($language->getId()),
                'description' => $item->getDescription($language->getId())
            ];
        }

        $this->resource->categories = $categories;
    }

    /**
     * @PUT categories
     */
    public function actionUpdate()
    {
        $data = $this->getHttpRequest()->getRawBody();
        $values = json_decode($data);
        $this->categoriesManager->updateCategory(
            $values->id,
            $values->name,
            $values->description,
            $this->languageManager->getDefaultLanguage(),
            $this->userManager->getUserById($this->getUser()->getId())
        );

        $this->resource->success = true;
    }

    /**
     * @POST categories
     */
    public function actionInsert()
    {
        $data = $this->getHttpRequest()->getRawBody();
        $values = json_decode($data);

        try {
            $this->categoriesManager->addCategory(
                $values->name,
                $values->description,
                $this->languageManager->getDefaultLanguage(),
                $this->userManager->getUserById($this->getUser()->getId())
            );
            $this->resource->success = true;
        } catch (Exception $e) {
            $this->resource->success = false;
            $this->resource->error = $e->getMessage();
        }
    }

    /**
     * @DELETE categories/<id>
     * @param int $id
     */
    public function actionDelete($id)
    {
        try {
            $this->categoriesManager->deleteCategory($id);
            $this->resource->success = true;
        } catch (Exception $e) {
            $this->resource->success = false;
            $this->resource->error = $e->getMessage();
        }
    }
}

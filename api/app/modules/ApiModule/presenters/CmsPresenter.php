<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\ApiModule;

use App\Model\Cms\Manager\CmsManager;
use App\Model\Language\Manager\LanguageManager;
use App\Model\Users\Manager\UserManager;
use Drahak\Restful\Application\UI\SecuredResourcePresenter;
use Exception;

/**
 * Class SeniorityPresenter
 * @package App\ApiModule
 */
class CmsPresenter extends SecuredResourcePresenter
{
    /** @var LanguageManager */
    private $languageManager;

    /** @var UserManager */
    private $userManager;

    /** @var CmsManager */
    private $cmsManager;

    /**
     * SeniorityPresenter constructor.
     * @param LanguageManager $languageManager
     * @param UserManager $userManager
     * @param CmsManager $cmsManager
     */
    public function __construct(
        LanguageManager $languageManager,
        UserManager $userManager,
        CmsManager $cmsManager
    )
    {
        parent::__construct();
        $this->userManager = $userManager;
        $this->languageManager = $languageManager;
        $this->cmsManager = $cmsManager;
    }

    /**
     * @GET cms
     */
    public function actionCms()
    {
        $cms = [];
        $language = $this->languageManager->getDefaultLanguage()->getId();
        foreach ($this->cmsManager->getCms() as $item) {
            $cms[] = [
                'id' => $item->getId(),
                'name' => $item->getName($language),
                'content' => $item->getContent($language)
            ];
        }
        $this->resource->cms = $cms;
    }

    /**
     * @GET cms/category/<id>
     */
    public function actionCategory($id)
    {
        $cms = [];
        $language = $this->languageManager->getDefaultLanguage()->getId();
        foreach ($this->cmsManager->getCmsBy(['category' => $id]) as $item) {
            $cms[] = [
                'id' => $item->getId(),
                'name' => $item->getName($language),
                'content' => $item->getContent($language)
            ];
        }
        $this->resource->cms = $cms;
    }

    /**
     * @POST cms
     */
    public function actionInsert()
    {
        $data = $this->getHttpRequest()->getRawBody();
        $values = json_decode($data);
        try {
            $this->cmsManager->addCms(
                $values->name,
                $values->content,
                $this->languageManager->getDefaultLanguage(),
                $values->categoryId
            );
            $this->resource->success = true;
        } catch (Exception $e) {
            $this->resource->success = false;
            $this->resource->error = $e->getMessage();
        }
    }

    /**
     * @PUT cms
     */
    public function actionUpdate()
    {
        $data = $this->getHttpRequest()->getRawBody();
        $values = json_decode($data);
        try {
            $this->cmsManager->updateCms(
                $values->id,
                $values->name,
                $values->content,
                $this->languageManager->getDefaultLanguage(),
                $values->categoryId
            );
            $this->resource->success = true;
        } catch (Exception $e) {
            $this->resource->success = false;
            $this->resource->error = $e->getMessage();
        }
    }

    /**
     * @DELETE cms/<id>
     * @param $id
     */
    public function actionDelete($id)
    {
        $this->cmsManager->deleteCms($id);
    }

    /**
     * @GET cms/detail/<id>
     */
    public function actionDetail($id)
    {
        $language = $this->languageManager->getDefaultLanguage()->getId();
        $item = $this->cmsManager->getCmsById($id);
        $this->resource->cms = [
            'id' => $item->getId(),
            'name' => $item->getName($language),
            'content' => $item->getContent($language)
        ];
    }
}

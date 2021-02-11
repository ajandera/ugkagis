<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\ApiModule;

use App\Model\Categories\Manager\CategoriesManager;
use App\Model\Cms\Manager\CmsManager;
use App\Model\Documents\Manager\DocumentsManager;
use App\Model\Email\Manager\EmailManager;
use App\Model\EmailTemplate\Manager\EmailTemplateManager;
use App\Model\Language\Manager\LanguageManager;
use App\Model\Settings\Manager\SettingManager;
use App\Model\StorageManager\StorageManager;
use App\Model\Users\Manager\UserManager;
use Drahak\Restful\Application\UI\ResourcePresenter;
use Exception;

/**
 * Class UnsecurePresenter
 * @package App\ApiModule
 */
class UnsecurePresenter extends ResourcePresenter
{
    /** @var LanguageManager */
    private $languageManager;

    /** @var UserManager */
    private $userManager;

    /** @var CmsManager */
    private $cmsManager;

    /** @var CategoriesManager */
    private $categoriesManager;

    /** @var SettingManager */
    private $settingsManager;

    /** @var EmailManager */
    private $emailManager;

    /** @var DocumentsManager */
    private $documentsManager;

    /** @var StorageManager */
    private $storageManager;


    /** @var EmailTemplateManager */
    private $emailTemplateManager;

    /**
     * BidsPresenter constructor.
     * @param LanguageManager $languageManager
     * @param UserManager $userManager
     * @param CmsManager $cmsManager
     * @param CategoriesManager $categoriesManager
     * @param SettingManager $settingManager
     * @param EmailManager $emailManager
     * @param DocumentsManager $documentsManager
     * @param StorageManager $storageManager
     * @param EmailTemplateManager $emailTemplateManager
     */
    public function __construct(
        LanguageManager $languageManager,
        UserManager $userManager,
        CmsManager $cmsManager,
        CategoriesManager $categoriesManager,
        SettingManager $settingManager,
        EmailManager $emailManager,
        DocumentsManager $documentsManager,
        StorageManager $storageManager,
        EmailTemplateManager $emailTemplateManager
    )
    {
        parent::__construct();
        $this->userManager = $userManager;
        $this->languageManager = $languageManager;
        $this->cmsManager = $cmsManager;
        $this->categoriesManager = $categoriesManager;
        $this->settingsManager = $settingManager;
        $this->emailManager = $emailManager;
        $this->documentsManager = $documentsManager;
        $this->storageManager = $storageManager;
        $this->emailTemplateManager = $emailTemplateManager;
    }

    /**
     * @GET front/settings
     */
    public function actionSettings()
    {
        $this->resource->settings = $this->settingsManager->getSettingsArray();
    }

    /**
     * @GET front/cms/<id>
     */
    public function actionCms($id)
    {
        $language = $this->languageManager->getDefaultLanguage()->getId();
        $item = $this->cmsManager->getCmsById($id);
        $this->resource->cms = [
            'id' => $item->getId(),
            'name' => $item->getName($language),
            'content' => $item->getContent($language)
        ];
    }

    /**
     * @GET front/categories/list/<id>
     * @param $id
     */
    public function actionCatCms($id)
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
     * @GET front/categories
     */
    public function actionCat()
    {
        $categories = [];
        $language = $this->languageManager->getDefaultLanguage()->getId();
        foreach ($this->categoriesManager->getCategoryBy(["enabled" => true]) as $item) {
            $categories[] = [
                'id' => $item->getId(),
                'name' => $item->getName($language),
                'description' => $item->getDescription($language)
            ];
        }
        $this->resource->categories = $categories;
    }

    /**
     * @GET front/role/<username>
     * @param $username
     */
    public function actionRole($username)
    {
        $user = $this->userManager->getUserByUserName($username);
        if ($user !== null) {
            $this->resource->user = [
                'enabled' => $user->getEnabled(),
                'role' => $user->getRole()
            ];
        } else {
            $this->resource->user = [
                'enabled' => false,
                'role' => false
            ];
        }
    }

    /**
     * Upload files.
     * @POST front/uploader
     */
    public function actionUploader()
    {
        try {
            $returnIds = [];
            foreach ($this->getHttpRequest()->getFiles() as $file) {
                $obj = $this->storageManager->addStorage($file->getName(), $file);
                $d = $this->documentsManager->addDocument(
                    $obj,
                    $file->getName(),
                    '',
                    $this->getUser()->getId()
                );
                $returnIds[] = $d->getId();
            }
            $this->resource->response = $returnIds;
        } catch (Exception $e) {
            $this->resource->response = $e->getMessage();
        }
    }

    /**
     * @GET front/unique/<username>
     * @param $username
     */
    public function actionUnique($username)
    {
        $user = $this->userManager->findOneBy(['username' => $username]);
        $this->resource->unique = $user === null ? true : false;
        $this->resource->deleted = $user !== null && $user->getDelete() === true ? true : false;
        $this->resource->id = $user !== null ? $user->getId() : null;
    }

    /**
     * @DELETE front/document/<id>
     * @param $id
     */
    public function actionDelDoc($id)
    {
        try {
            $this->documentsManager->deleteDocument($id);
            $this->resource->success = true;
        } catch (Exception $e) {
            $this->resource->success = false;
        }
    }
}

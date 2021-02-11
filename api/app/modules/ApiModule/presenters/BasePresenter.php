<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\ApiModule;

use App\Model\Categories\Manager\CategoriesManager;
use App\Model\Cms\Manager\CmsManager;
use App\Model\Email\Manager\EmailManager;
use App\Model\Language\Manager\LanguageManager;
use App\Model\Settings\Manager\SettingManager;
use App\Model\Users\Manager\UserManager;
use Drahak\Restful\Application\UI\SecuredResourcePresenter;

/**
 * Class BasePresenter
 * @package App\ApiModule
 */
class BasePresenter extends SecuredResourcePresenter
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

    /**
     * BidsPresenter constructor.
     * @param LanguageManager $languageManager
     * @param UserManager $userManager
     * @param CmsManager $cmsManager
     * @param CategoriesManager $categoriesManager
     * @param SettingManager $settingManager
     * @param EmailManager $emailManager
     */
    public function __construct(
        LanguageManager $languageManager,
        UserManager $userManager,
        CmsManager $cmsManager,
        CategoriesManager $categoriesManager,
        SettingManager $settingManager,
        EmailManager $emailManager
    )
    {
        parent::__construct();
        $this->userManager = $userManager;
        $this->languageManager = $languageManager;
        $this->cmsManager = $cmsManager;
        $this->categoriesManager = $categoriesManager;
        $this->settingsManager = $settingManager;
        $this->emailManager = $emailManager;
    }

    /**
     * @GET base/settings
     */
    public function actionSettings()
    {
        $this->resource->settings = $this->settingsManager->getSettingsArray();
    }

    /**
     * @POST base/email
     */
    public function actionEmail()
    {
        $data = $this->getHttpRequest()->getRawBody();
        $values = json_decode($data);
        try {
            $this->emailManager->createEmail(
                $values->email,
                $values->subject,
                $values->body,
                [],
                $values->priority
            );
            foreach ($values->bcc as $e) {
                $this->emailManager->createEmail(
                    $e,
                    $values->subject,
                    $values->body,
                    [],
                    $values->priority
                );
            }
            $this->resource->success = true;
        } catch (\Exception $e) {
            $this->resource->success = false;
        }
    }
}

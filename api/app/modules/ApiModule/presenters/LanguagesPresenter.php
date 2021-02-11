<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\ApiModule;

use App\Model\Language\Manager\LanguageManager;
use App\Model\Users\Manager\UserManager;
use Drahak\Restful\Application\UI\SecuredResourcePresenter;
use Exception;

/**
 * Class LanguagesPresenter
 * @package App\ApiModule
 */
class LanguagesPresenter extends SecuredResourcePresenter
{
    /** @var LanguageManager */
    private $languageManager;

    /** @var UserManager */
    private $userManager;

    /**
     * LanguagePresenter constructor.
     * @param LanguageManager $languageManager
     * @param UserManager $userManager
     */
    public function __construct(
        LanguageManager $languageManager,
        UserManager $userManager
    )
    {
        parent::__construct();
        $this->userManager = $userManager;
        $this->languageManager = $languageManager;
    }

    /**
     * @GET languages
     */
    public function actionLanguages()
    {
        $languages = [];
        foreach ($this->languageManager->getLanguages() as $item) {
            $languages[] = [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'code' => $item->getCode(),
                'translation_code' => $item->getTranslationCode(),
                'defaults' => $item->getDefaults(),
                'visible' => $item->getVisible()
            ];
        }
        $this->resource->languages = $languages;
    }

    /**
     * @POST languages
     */
    public function actionInsert()
    {
        $data = $this->getHttpRequest()->getRawBody();
        $values = json_decode($data);
        try {
            $this->languageManager->addLanguage(
                $values->name,
                $values->code,
                isset($values->defaults) ? $values->defaults : false,
                $this->userManager->getUserById($this->getUser()->getId()),
                $values->translationCode,
                isset($values->visible) ? $values->visible : false
            );
            $this->resource->success = true;
        } catch (Exception $e) {
            $this->resource->success = false;
            $this->resource->error = $e->getMessage();
        }
    }

    /**
     * @PUT languages
     */
    public function actionUpdate()
    {
        $data = $this->getHttpRequest()->getRawBody();
        $values = json_decode($data);
        try {
            $this->languageManager->updateLanguage(
                $values->id,
                $values->name,
                $values->code,
                $values->defaults,
                $this->userManager->getUserById($this->getUser()),
                $values->translationCode,
                $values->visible

            );
            $this->resource->success = true;
        } catch (Exception $e) {
            $this->resource->success = false;
            $this->resource->error = $e->getMessage();
        }
    }

    /**
     * @DELETE languages/<id>
     * @param $id
     */
    public function actionDelete($id)
    {
        $this->languageManager->deleteLanguage($id);
    }
}

<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\ApiModule;

use App\Model\EmailTemplate\Manager\EmailTemplateManager;
use App\Model\Language\Manager\LanguageManager;
use App\Model\Users\Manager\UserManager;
use Drahak\Restful\Application\UI\SecuredResourcePresenter;
use Exception;

/**
 * Class EmailTemplatesPresenter
 * @package App\ApiModule
 */
class EmailTemplatesPresenter extends SecuredResourcePresenter
{
    /** @var LanguageManager */
    private $languageManager;

    /** @var UserManager */
    private $userManager;

    /** @var EmailTemplateManager */
    private $emailTemplatesManager;

    /**
     * EmailTemplatesPresenter constructor.
     * @param LanguageManager $languageManager
     * @param UserManager $userManager
     * @param EmailTemplateManager $emailTemplateManager
     */
    public function __construct(
        LanguageManager $languageManager,
        UserManager $userManager,
        EmailTemplateManager $emailTemplateManager
    )
    {
        parent::__construct();
        $this->emailTemplatesManager = $emailTemplateManager;
        $this->userManager = $userManager;
        $this->languageManager = $languageManager;
    }

    /**
     * @GET email-templates
     */
    public function actionEmailTemplates()
    {
        $templates = [];
        $language = $this->languageManager->getDefaultLanguage()->getId();
        foreach ($this->emailTemplatesManager->getEmailTemplates() as $item) {
            $templates[] = [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'subject' => $item->getSubject($language),
                'body' => $item->getBody($language),
                'type' => $item->getType()
            ];
        }
        $this->resource->templates = $templates;
    }

    /**
     * @POST email-templates
     */
    public function actionInsert()
    {
        $data = $this->getHttpRequest()->getRawBody();
        $values = json_decode($data);
        try {
            $this->emailTemplatesManager->addEmailTemplate(
                $values->name,
                isset($values->subject) ? $values->subject : '',
                $values->body,
                $this->languageManager->getDefaultLanguage(),
                $this->userManager->getUserById($this->getUser()->getId()),
                $values->type
            );
            $this->resource->success = true;
        } catch (Exception $e) {
            $this->resource->success = false;
            $this->resource->error = $e->getMessage();
        }
    }

    /**
     * @PUT email-templates
     */
    public function actionUpdate()
    {
        $data = $this->getHttpRequest()->getRawBody();
        $values = json_decode($data);
        try {
            $this->emailTemplatesManager->updateEmailTemplate(
                $values->id,
                $values->name,
                isset($values->subject) ? $values->subject : '',
                $values->body,
                $this->languageManager->getDefaultLanguage(),
                $this->userManager->getUserById($this->getUser()->getId()),
                $values->type
            );
            $this->resource->success = true;
        } catch (Exception $e) {
            $this->resource->success = false;
            $this->resource->error = $e->getMessage();
        }
    }

    /**
     * @DELETE email-templates/<id>
     * @param $id
     */
    public function actionDelete($id)
    {
        $this->emailTemplatesManager->deleteEmailTemplate($id);
    }
}

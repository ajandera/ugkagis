<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\ApiModule;

use App\ePro\Authenticator;
use App\Model\EmailTemplate\Entity\EmailTemplates;
use App\Model\Users\Manager\UserManager;
use Nette\Security\AuthenticationException;
use Nette\Utils\Random;
use App\Model\Settings\Manager\SettingManager;
use App\Model\EmailTemplate\Manager\EmailTemplateManager;
use App\Model\Email\Manager\EmailManager;
use App\Model\Language\Manager\LanguageManager;
use Drahak\Restful\Application\UI\ResourcePresenter;

/**
 * Class SecurityPresenter
 * @package App\ApiModule
 */
class SecurityPresenter extends ResourcePresenter
{
    /** @var  Authenticator */
    private $authenticator;

    /** @var  UserManager */
    private $userManager;

    /** @var SettingManager */
    private $settingsManager;

    /** @var EmailTemplateManager * */
    private $emailTemplateManager;

    /** @var EmailManager * */
    private $emailManager;

    /** @var LanguageManager * */
    private $languageManager;

    /**
     * SecurityPresenter constructor.
     * @param Authenticator $authenticator
     * @param UserManager $userManager
     * @param SettingManager $settingsManager
     * @param EmailTemplateManager $emailTemplateManager
     * @param EmailManager $emailManager
     * @param LanguageManager $languageManager
     */
    public function __construct(
        Authenticator $authenticator,
        UserManager $userManager,
        SettingManager $settingsManager,
        EmailTemplateManager $emailTemplateManager,
        EmailManager $emailManager,
        LanguageManager $languageManager
    )
    {
        parent::__construct();
        $this->authenticator = $authenticator;
        $this->userManager = $userManager;
        $this->settingsManager = $settingsManager;
        $this->emailTemplateManager = $emailTemplateManager;
        $this->emailManager = $emailManager;
        $this->languageManager = $languageManager;
    }

    /**
     * @GET security/profile
     */
    public function actionProfile()
    {
        if ($this->getUser()->isLoggedIn()) {
            $user = $this->userManager->getUserById($this->getUser()->getId());
            $this->resource->user = [
                'name' => $user->getFullName(),
                'username' => $user->getUsername(),
                'role' => $user->getRole(),
                'id' => $user->getId()
            ];
        } else {
            $this->resource->user = [];
        }
    }

    /**
     * @POST security/reset-password
     */
    public function actionResetPassword()
    {
        $data = $this->getHttpRequest()->getRawBody();
        $values = json_decode($data);
        $user = $this->userManager->findOneBy(['username' => $values->email, 'role' => $values->role]);
        $language = $this->languageManager->getLanguageByCode('en');
        if ($user) {
            $token = Random::generate(15);
            $this->userManager->setRestorePassword($user, $token);
            $template = $this->emailTemplateManager->getEmailTemplateBy(['type' => EmailTemplates::FORGOT]);
            $url = 'https://' . $_SERVER['HTTP_HOST'] . '/#/login/restore?token=' . $token;
            $attachments = [];
            foreach ($template->getDocuments() as $attach) {
                $attachments[] = $attach->getSrc();
            }
            $this->emailManager->createEmail(
                $values->email,
                $template->getSubject($language->getId()),
                str_replace("%link%", $url, $template->getBody($language->getId())),
                [],
                1,
                $attachments
            );

            $this->resource->success = true;
        } else {
            $this->resource->success = false;
        }
    }

    /**
     * @POST security/login
     */
    public function actionLogin()
    {
        $data = $this->getHttpRequest()->getRawBody();
        $values = json_decode($data);

        try {
            $this->authenticator->login(
                $values->username,
                $values->password,
                $values->role
            );

            $user = $this->userManager->getUserById($this->getUser()->getId());
            $this->resource->user = [
                'name' => $user->getFullName(),
                'username' => $user->getUsername(),
                'role' => $user->getRole(),
                'id' => $user->getId()
            ];
        } catch (AuthenticationException $e) {
            $this->resource->user = null;
            $this->resource->error = $e->getMessage();
        }
    }

    /**
     * @GET security/logout
     */
    public function actionLogout()
    {
        try {
            $this->getUser()->logout(true);
            $this->resource->message = true;
        } catch (\Exception $e) {
            $this->resource->message = $e->getMessage();
        }
    }

    /**
     * @POST security/change-password
     */
    public function actionChangePassword()
    {
        $data = $this->getHttpRequest()->getRawBody();
        $values = json_decode($data);
        $response = $this->userManager->setPassword($values->id, $values->password);
        $this->resource->message = $response;
    }

    /**
     * @POST security/check-token
     */
    public function actionCheckToken()
    {
        $data = $this->getHttpRequest()->getRawBody();
        $values = json_decode($data);
        $user = $this->userManager->getUserByToken($values->token);
        if ($user !== null && $user->isTokenValidDate() === true) {
            $this->resource->success = true;
        } else {
            $this->resource->success = false;
        }
    }

    /**
     * @POST security/restore-password
     */
    public function actionRestorePassword()
    {
        $data = $this->getHttpRequest()->getRawBody();
        $values = json_decode($data);
        $user = $this->userManager->getUserByToken($values->token);

        $response = $this->userManager->setPassword($user, $values->password);
        $this->resource->message = $response;
    }

    /**
     * @POST security/register-email
     */
    public function actionRegisterEmail()
    {
        $data = $this->getHttpRequest()->getRawBody();
        $values = json_decode($data);
        $key = 'register-' . $values->type . '-template';
        $templateSetting = $this->settingsManager->getSettingByKey($key);
        if ($templateSetting !== null) {
            $templateId = $templateSetting->getValue();
            $template = $this->emailTemplateManager->getEmailTemplateById($templateId);
            $language = $this->languageManager->getLanguageByCode('en');

            $entity = $this->userManager->getUserById($values->id);
            $name = $entity->getFullName();
            $date = $entity->getCreated()->format('j.n.Y H:i');
            $username = $entity->getUsername();
            $body = str_replace("%name%", $name, $template->getBody($language->getId()));
            $body = str_replace("%date%", $date, $body);
            $body = str_replace("%username%", $username, $body);
            $email = $username;

            $attachments = [];
            foreach ($template->getDocuments() as $attach) {
                $attachments[] = $attach->getSrc();
            }

            $this->emailManager->createEmail(
                $email,
                $template->getSubject($language->getId()),
                $body,
                [],
                1,
                $attachments
            );
        }

        $this->resource->message = true;
    }
}

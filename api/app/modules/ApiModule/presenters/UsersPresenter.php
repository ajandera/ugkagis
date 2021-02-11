<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\ApiModule;

use App\Model\Language\Manager\LanguageManager;
use App\Model\Users\Entity\Users;
use App\Model\Users\Manager\UserManager;
use Drahak\Restful\Application\UI\SecuredResourcePresenter;
use Exception;

/**
 * Class UsersPresenter
 * @package App\ApiModule
 */
class UsersPresenter extends SecuredResourcePresenter
{
    /** @var LanguageManager */
    private $languageManager;

    /** @var UserManager */
    private $userManager;

    /**
     * UsersPresenter constructor.
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
     * @GET users
     */
    public function actionUsers()
    {
        $users = [];
        foreach ($this->userManager->getUsers() as $item) {
            $users[] = [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'surname' => $item->getSurname(),
                'username' => $item->getUsername(),
                'role' => $item->getRole(),
                'phone' => $item->getPhone(),
                'enabled' => $item->getEnabled()
            ];
        }
        $this->resource->users = $users;
    }

    /**
     * @POST users
     */
    public function actionInsert()
    {
        $data = $this->getHttpRequest()->getRawBody();
        $values = json_decode($data);
        try {
            $this->userManager->addUser(
                $values->username,
                $values->password,
                $values->name,
                $values->surname,
                $this->userManager->getUserById($this->getUser()->getId()),
                isset($values->enabled) ? $values->enabled : false,
                $values->role,
                isset($values->phone) ? $values->phone : null,
                $values->description,
                $this->languageManager->getDefaultLanguage()->getId()
            );
            $this->resource->success = true;
        } catch (Exception $e) {
            $this->resource->success = false;
            $this->resource->error = $e->getMessage();
        }
    }

    /**
     * @POST employee
     */
    public function actionEmployee()
    {
        $data = $this->getHttpRequest()->getRawBody();
        $values = json_decode($data);
        try {
            $this->userManager->addUser(
                $values->username,
                $values->password,
                $values->name,
                $values->surname,
                $this->userManager->getUserById($this->getUser()->getId()),
                isset($values->enabled) ? $values->enabled : false,
                Users::EMPLOYEE,
                isset($values->phone) ? $values->phone : null,
                $values->description,
                $this->languageManager->getDefaultLanguage()->getId()
            );
            $this->resource->success = true;
        } catch (Exception $e) {
            $this->resource->success = false;
            $this->resource->error = $e->getMessage();
        }
    }

    /**
     * @PUT users
     */
    public function actionUpdate()
    {
        $data = $this->getHttpRequest()->getRawBody();
        $values = json_decode($data);
        if (isset($values->notifications)) {
            $notifications = [
                "job_partner" => $values->notifications->job_partner,
                "job_operator" => $values->notifications->job_operator,
                "job_update_partner" => $values->notifications->job_update_partner,
                "job_update_operator" => $values->notifications->job_update_operator,
                "job_reaction" => $values->notifications->job_reaction
            ];
        } else {
            $notifications = null;
        }

        try {
            $this->userManager->editUser(
                $values->id,
                $values->name,
                $values->surname,
                $this->userManager->getUserById($this->getUser()->getId()),
                $values->enabled,
                $values->phone
            );
            $this->resource->success = true;
        } catch (Exception $e) {
            $this->resource->success = false;
            $this->resource->error = $e->getMessage();
        }
    }

    /**
     * @DELETE users/<id>
     * @param $id
     */
    public function actionDelete($id)
    {
        try {
            $this->userManager->deleteUser($id);
            $this->resource->success = true;
        } catch (Exception $e) {
            $this->resource->success = false;
            $this->resource->error = $e->getMessage();
        }
    }

    /**
     * @GET users/unique/<username>
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
     * @GET users/role/<username>
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
     * @GET users/detail/<id>
     * @param int $id
     */
    public function actionDetail($id)
    {
        $detail = [];
        $user = $this->userManager->getUserById($id);
        $detail['id'] = $user->getId();
        $detail['username'] = $user->getUsername();
        $detail['phone'] = $user->getPhone();
        $detail['name'] = $user->getName();
        $detail['surname'] = $user->getSurname();
        $detail['description'] = $user->getDescription($this->languageManager->getDefaultLanguage()->getId());
        $this->resource->detail = $detail;
    }
}

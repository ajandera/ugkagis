<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\Model\Users\Manager;

use App\Model\Categories\Storage\ICategoriesDao;
use App\Model\Users\Entity\Users;
use App\Model\Users\Storage\IUsersDao;
use Nette\Security\AuthenticationException;
use Nette\Security\Identity;
use Nette\Security\Passwords;
use Nette;
use Nette\Utils\DateTime;

/**
 * Users management.
 */
class UserManager implements Nette\Security\IAuthenticator
{
    /** @var IUsersDao */
    private $user;

    /** @var  ICategoriesDao */
    private $categoriesDao;

    /**
     * UserManager constructor.
     * @param IUsersDao $user
     * @param ICategoriesDao $categoriesDao
     */
    public function __construct(
        IUsersDao $user,
        ICategoriesDao $categoriesDao
    )
    {
        $this->user = $user;
        $this->categoriesDao = $categoriesDao;
    }

    const
        TABLE_NAME = 'users',
        COLUMN_ID = 'id',
        COLUMN_NAME = 'username',
        COLUMN_PASSWORD = 'password',
        COLUMN_ROLE = 'role',
        PASSWORD_MAX_LENGTH = 4096;

    /**
     * Performs an authentication.
     * @param array $credentials
     * @param null $role
     * @return Nette\Security\Identity
     * @throws Nette\Security\AuthenticationException
     */
    public function authenticate(array $credentials, $role = null)
    {
        list($username, $password) = $credentials;
        $row = $this->getUserByUserName($username, $role);

        if (!$row) {
            throw new AuthenticationException('Email not found');
        } elseif (!Passwords::verify($password, $row->getPassword())) {
            throw new AuthenticationException('Password not correct');
        } elseif($row->getEnabled() === false) {
            throw new AuthenticationException('This account is disabled');
        }

        return new Identity($row->getId(), $row->getRole(), ['name' => $row->getFullName(), 'username' => $row->getUsername()]);
    }

    /**
     * @param $username
     * @param null $role
     * @return Users
     */
    public function getUserByUserName($username, $role = null)
    {
        return $this->user->findByName($username, $role);
    }

    /**
     * @param $id
     * @return Users
     */
    public function getUserById($id)
    {
        return $this->user->findById($id);
    }

    /**
     * @return Users[]
     */
    public function getUsers()
    {
        return $this->user->findBy(['role' => [0,1], 'dgdelete' => 0]);
    }

    /**
     * @param $id
     * @param $password
     * @return int|string
     */
    public function setPassword($id, $password)
    {
        try {
            if ($id instanceof Users) {
                $user = $id;
            } else {
                $user = $this->getUserById($id);
            }
            $user->setPassword($password);
            $this->user->save($user);
            return $user->getId();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param $email
     * @param $password
     * @param $name
     * @param $surname
     * @param $loggedUser
     * @param $enabled
     * @param null $role
     * @param null $phone
     * @param null $description
     * @param null $language
     * @return Users
     */
    public function addUser(
        $email,
        $password,
        $name,
        $surname,
        $loggedUser,
        $enabled,
        $role = null,
        $phone = null,
        $description = null,
        $language = null
    )
    {
        $newUser = new Users;
        $newUser->setUsername($email);
        $newUser->setPassword($password);
        $newUser->setRole(Users::ADMIN);
        $newUser->setName($name);
        $newUser->setSurname($surname);
        $newUser->setCreated();
        $newUser->setModified();
        $newUser->setRole($role);
        $newUser->setUsers($loggedUser);
        $newUser->setEnabled($enabled);
        if($phone !== null) {
            $newUser->setPhone($phone);
        }
        $newUser->setDescription($description, $language);
        $this->user->save($newUser);
        return $newUser;
    }

    /**
     * @return mixed
     */
    public function getRealIpAddress()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

    /**
     * @param $id
     * @param $name
     * @param $surname
     * @param $loggedUser
     * @param $enabled
     * @param $notifications
     * @param $phone
     * @param $smtpUrl
     * @param $smtpUser
     * @param $smtpPassword
     * @param $smtpPort
     * @param $signature
     * @return Users
     */
    public function editUser(
        $id,
        $name,
        $surname,
        $loggedUser,
        $enabled,
        $phone
    )
    {
        $user = $this->getUserById($id);
        $user->setName($name);
        $user->setSurname($surname);
        $user->setModified();
        $user->setUsersModified($loggedUser);
        $user->setEnabled($enabled);
        $user->setPhone($phone);
        $this->user->hardSave($user);
        return $user;
    }

    /**
     * Return all users
     * @return Users[]
     */
    public function findAll()
    {
        return $this->user->findBy(['role' => Users::ADMIN]);
    }

    /**
     * @param string $token
     * @return Users
     */
    public function getUserByToken($token)
    {
        return $this->user->findOneBy(["restorePasswordToken" => $token]);
    }

    /**
     * @param Users $user
     * @param string $token
     */
    public function setRestorePassword($user, $token)
    {
        $user->setRestorePasswordToken($token);
        $user->setTokenValidDate();
        $this->user->save($user);
    }

    /**
     * @param $token
     * @param $password
     * @return bool
     */
    public function restorePassword($token, $password)
    {
        $user = $this->getUserByUserName($token);
        if ($user->isTokenValidDate()) {
            $user->setPassword($password);
            $user->setRestorePasswordToken(null);
            $this->user->save($user);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get users by parameters
     * @param array $conditions
     * @param array $order
     * @param null $limit
     * @param null $offset
     * @return Users[]
     */
    public function findBy($conditions, $order = [], $limit = null, $offset = null)
    {
        return $this->user->findBy($conditions, $order, $limit, $offset);
    }
    
    /**
     * @param array $conditions
     * @return \App\Model\Users\Entity\Users
     */
    public function findOneBy($conditions)
    {
        return $this->user->findOneBy($conditions);
    }

    /**
     * Delete user by id.
     * @param $id
     */
    public function deleteUser($id)
    {
        $user = $this->getUserById($id);
        $user->setDelete(true);
        $this->user->save($user);
    }

    /**
     * Delete contact
     * @param $id
     */
    public function deleteContact($id)
    {
        $user = $this->getUserById($id);
        $user->setDelete(1);
        $this->user->save($user);
    }

    /**
     * @param $id
     */
    public function reCreateUser($id)
    {
        $user = $this->getUserById($id);
        $user->setDelete(0);
        $this->user->save($user);
    }
}

<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\ePro;

use App\Model\Users\Manager\UserManager;
use Nette\Security\AuthenticationException;
use Nette\Security\Identity;
use Nette\Security\IIdentity;
use Nette\Security\User;

class Authenticator
{

    /** @var User */
    private $user;

    /** @var  UserManager */
    private $userManager;

    public function __construct(User $user, UserManager $userManager)
    {
        $this->user = $user;
        $this->userManager = $userManager;
    }

    /**
     * ePro authenticator.
     * @param null $id
     * @param null $password
     * @param $role
     * @param bool $socialLogin
     * @throws AuthenticationException
     */
    public function login($id = null, $password = null, $role, $socialLogin = false)
    {
        $this->user->logout(true);

        if($socialLogin == true) {
            $user = $this->userManager->getUserByUserName($id, $role);
            if($user == null) {
                throw new AuthenticationException('Social login failed');
            } else {
                if ($user->getEnabled() === true) {
                    $id = new Identity($user->getId(), $user->getRole(), ['name' => $user->getFullName(), 'username' => $user->getUsername()]);
                } else {
                    throw new AuthenticationException('Account has been disabled.');
                }
            }
        } else {
            if (!$id instanceof IIdentity) {
                $id = $this->userManager->authenticate(func_get_args(), $role);
            }
        }

        $this->user->getStorage()->setIdentity($id);
        $this->user->getStorage()->setAuthenticated(true);
        $this->user->onLoggedIn($this);
    }

}

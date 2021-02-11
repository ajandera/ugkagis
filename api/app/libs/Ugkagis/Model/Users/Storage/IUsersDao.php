<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\Model\Users\Storage;

use App\Model\Users\Entity\Users;

/**
 * Interface IUsersDao
 * @package App\Model\Users\Storage
 */
interface IUsersDao
{
    /**
     * Save persist users to storage.
     * @param Users $users
     */
    public function save(Users $users);

    /**
     * Persist users to storage.
     * @param Users $users
     */
    public function hardSave(Users $users);

    /**
     * Delete users from storage.
     * @param Users $users
     */
    public function delete(Users $users);

    /**
     * Find all users by parameters
     * @return Users[]
     */
    public function findAll();

    /**
     * Find users by id
     * @param int $usersId
     * @return Users
     */
    public function findById($usersId);

    /**
     * @param $username
     * @param $role
     * @return Users
     */
    public function findByName($username, $role);

    /**
     * Returns count of all users.
     * @param array $conditions
     * @return int
     */
    public function countOfAll($conditions = []);

    /**
     * Return Users by conditions.
     * @param array $conditions
     * @return Users
     */
    public function findOneBy($conditions = []);

    /**
     * Find users by conditions.
     * @param array $conditions
     * @param array $order
     * @param null $limit
     * @param null $offset
     * @return Users[]
     */
    public function findBy($conditions = [], $order = [], $limit = null, $offset = null);

    /**
     * @param $languageId
     * @return mixed
     */
    public function getConsultantList($languageId);
}

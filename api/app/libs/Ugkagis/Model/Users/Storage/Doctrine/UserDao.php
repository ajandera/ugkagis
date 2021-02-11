<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\Model\Users\Storage\Doctrine;

use App\Model\Users\Entity\Users;
use App\Model\Users\Storage\IUsersDao;
use Doctrine\ORM\Mapping;
use Kdyby\Doctrine\EntityDao AS DoctrineEntityDao;
use PDO;

/**
 * Class UsersDao
 * @package App\Model\Users\Storage\Doctrine
 */
class UsersDao implements IUsersDao
{
    /** @var DoctrineEntityDao */
    private $usersDao;

    public function __construct(DoctrineEntityDao $usersDao)
    {
        $this->usersDao = $usersDao;
    }

    /**
     * Persist users to storage.
     * @param Users $users
     * @throws \Exception
     */
    public function save(Users $users)
    {
        $this->usersDao->getEntityManager()->persist($users);
        $this->usersDao->getEntityManager()->flush();
    }

    /**
     * @param Users $user
     * @throws \Exception
     */
    public function hardSave(Users $user)
    {
        $this->usersDao->getEntityManager()->persist($user);
        $this->usersDao->getEntityManager()->flush();
    }

    /**
     * Delete Users from storage.
     * @param Users $users
     * @throws \Exception
     */
    public function delete(Users $users)
    {
        $this->usersDao->getEntityManager()->remove($users);
        $this->usersDao->getEntityManager()->flush();
    }

    /**
     * Find all users by parameters
     * @return Users[]
     */
    public function findAll()
    {
        return $this->usersDao->findAll();
    }

    /**
     * Find user by id
     * @param int $userId
     * @return Users|object
     */
    public function findById($userId)
    {
        return $this->usersDao->find($userId);
    }

    /**
     * Returns count of all users.
     * @param array $conditions
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countOfAll($conditions = [])
    {
        $qb = $this->usersDao->createQueryBuilder('t');
        $qb->select('count(t.id)');
        $qb->whereCriteria($conditions);
        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param $username
     * @param $role
     * @return Users
     */
    public function findByName($username, $role)
    {
        if ($role) {
            return $this->usersDao->findOneBy(['username' => $username, 'role' => $role]);
        } else {
            return $this->usersDao->findOneBy(['username' => $username]);
        }
    }

    /**
     * @param array $conditions
     * @return Users
     */
    public function findOneBy($conditions = [])
    {
        return $this->usersDao->findOneBy($conditions);
    }

    /**
     * @param array $conditions
     * @param array $order
     * @param null $limit
     * @param null $offset
     * @return Users[]
     */
    public function findBy($conditions = [], $order = [], $limit = null, $offset = null)
    {
        return $this->usersDao->findBy($conditions, $order, $limit, $offset);
    }

    /**
     * @param $languageId
     * @return mixed
     */
    public function getConsultantList($languageId)
    {
        $selectName = "TRIM(BOTH '\"' FROM c.name -> '$.\"{$languageId}\"')";
        $sql = "
SELECT u.*, GROUP_CONCAT({$selectName} SEPARATOR ', ') AS categories
FROM users AS u
LEFT JOIN users_categories AS uc ON uc.users_id = u.id
LEFT JOIN categories AS c ON c.id = uc.categories_id  
WHERE role = :role 
AND dgdelete = 0
GROUP BY u.id
ORDER BY u.surname ASC, u.name ASC
";
        $consultants = $this->usersDao->getEntityManager()->getConnection()->prepare($sql);
        $param['role'] = Users::CONSULTANT;
        $consultants->execute($param);
        return $consultants->fetchAll(PDO::FETCH_ASSOC);
    }
}

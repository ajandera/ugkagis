<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\Model\Email\Storage;

use App\Model\Email\Entity\Email;

/**
 * Interface IEmailDao
 * @package App\Model\Email\Storage
 */
interface IEmailDao
{
    /**
     * Persist Email to storage.
     * @param Email $Email
     */
    public function save(Email $Email);

    /**
     * Delete Email from storage.
     * @param Email $Email
     */
    public function delete(Email $Email);

    /**
     * Find all Email.
     * @return Email[]
     */
    public function findAll();

    /**
     * Find Email by condition.
     * @param array $condition
     * @param $order
     * @param $limit
     * @param $offset
     * @return Email[]
     */
    public function findBy($condition, $order, $limit, $offset);
    
    /**
     * Find Email by condition.
     * @param array $condition
     * @return Email
     */
    public function findOneBy($condition);

    /**
     * Find Email by id
     * @param int $EmailId
     * @return Email
     */
    public function findById($EmailId);

    /**
     * Returns count of all Email.
     * @param array $conditions
     * @return int
     */
    public function countOfAll($conditions = []);
}

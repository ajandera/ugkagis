<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\Model\Cms\Storage;

use App\Model\Cms\Entity\Cms;

/**
 * Interface ICmsDao
 * @package App\Model\Cms\Storage
 */
interface ICmsDao
{
    /**
     * Persist cms to storage.
     * @param Cms $cms
     */
    public function save(Cms $cms);

    /**
     * Delete cms from storage.
     * @param Cms $cms
     */
    public function delete(Cms $cms);

    /**
     * Find all cms.
     * @return Cms[]
     */
    public function findAll();

    /**
     * Find all cms by condition.
     * @param array $condition
     * @return Cms[]
     */
    public function findBy($condition = []);

    /**
     * Find cms by id
     * @param int $cmsId
     * @return Cms
     */
    public function findById($cmsId);

    /**
     * Returns count of all cms.
     * @param array $conditions
     * @return int
     */
    public function countOfAll($conditions = []);
}

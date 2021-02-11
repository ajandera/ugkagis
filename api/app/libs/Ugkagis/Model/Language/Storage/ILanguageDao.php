<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\Model\Language\Storage;

use App\Model\Language\Entity\Language;

/**
 * Interface ILanguageDao
 * @package App\Model\Language\Storage
 */
interface ILanguageDao
{
    /**
     * Persist language to storage.
     * @param Language $language
     */
    public function save(Language $language);

    /**
     * Delete language from storage.
     * @param Language $language
     */
    public function delete(Language $language);

    /**
     * Find all language by parameters
     * @param int $offset
     * @param int $limit
     * @param array $conditions
     * @param array $order
     * @return Language[]
     */
    public function findBy($conditions = [], $order = [], $offset = null, $limit = null );

    /**
     * Find language by id
     * @param int $languageId
     * @return Language
     */
    public function findById($languageId);

    /**
     * Returns count of all language.
     * @param array $conditions
     * @return int
     */
    public function countOfAll($conditions = []);

    /**
     * Find language by conditions.
     * @param array $conditions
     * @return Language
     */
    public function findOneBy($conditions = []);
}

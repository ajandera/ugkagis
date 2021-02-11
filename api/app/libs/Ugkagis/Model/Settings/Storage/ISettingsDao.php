<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\Model\Settings\Storage;

use App\Model\Settings\Entity\Settings;

/**
 * Interface ISettingsDao
 * @package App\Model\Settings\Storage
 */
interface ISettingsDao
{
    /**
     * Persist settings to storage.
     * @param Settings $settings
     */
    public function save(Settings $settings);

    /**
     * Delete settings from storage.
     * @param Settings $settings
     */
    public function delete(Settings $settings);

    /**
     * Find all settings.
     * @return Settings[]
     */
    public function findAll();

    /**
     * Find settings by id
     * @param int $settingsId
     * @return Settings
     */
    public function findById($settingsId);

    /**
     * Find settings by conditions.
     * @param array $conditions
     * @return Settings[]
     */
    public function findBy($conditions = []);

    /**
     * Find one settings by conditions.
     * @param array $conditions
     * @return Settings
     */
    public function findOneBy($conditions = []);

    /**
     * Returns count of all settings.
     * @param array $conditions
     * @return int
     */
    public function countOfAll($conditions = []);
}

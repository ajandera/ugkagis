<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\Model\Settings\Manager;

use App\Model\Language\Manager\LanguageManager;
use App\Model\StorageManager\StorageManager;
use App\Model\Settings\Entity\Settings;
use App\Model\Settings\Storage\ISettingsDao;
use App\Model\Users\Manager\UserManager;

/**
 * Class SettingManager
 * @package App\Model\Settings\Manager
 */
class SettingManager
{
    /** @var  ISettingsDao */
    private $settingsDao;

    /** @var  UserManager */
    private $userManager;

    /** @var StorageManager */
    private $storageManager;

    /** @var  LanguageManager */
    private $languageManager;
    
    public function __construct(
        UserManager $userManager,
        StorageManager $storageManager,
        ISettingsDao $settingsDao,
        LanguageManager $languageManager
    )
    {
        $this->settingsDao = $settingsDao;
        $this->userManager = $userManager;
        $this->storageManager = $storageManager;
        $this->languageManager = $languageManager;
    }

    /**
     * @param $user
     * @return Settings[]
     */
    public function getSettings($user)
    {
        if($user) {
            return $this->settingsDao->findBy(['users' => $user]);
        } else {
            return $this->settingsDao->findAll();
        }
    }
    
    /**
     * @param string $key
     * @return Settings
     */
    public function getSettingByKey($key)
    {
        return $this->settingsDao->findOneBy(['keyString' => $key]);
    }

    /**
     * @param $values
     * @param $user
     */
    public function updateSetting($values, $user)
    {
        foreach($values as $key => $value) {
            if ($this->getSettingByKey($key)) {
                $setting = $this->getSettingByKey($key);
                $setting->setValue($value);
                $setting->setModified();
                $setting->setUsersModified($user);
                $this->settingsDao->save($setting);
            } else {
                $setting = new Settings;
                $setting->setKey($key);
                $setting->setValue(($value == "" && $value != 0) ? null : $value);
                $setting->setCreated();
                $setting->setModified();
                $setting->setUsersModified($user);
                $setting->setUsers($user);
                $this->settingsDao->save($setting);
            }
        }
    }


    /**
     * Update setting by key.
     * @param $key
     * @param $value
     * @param null $user
     */
    public function updateSettingByKey($key, $value, $user = null)
    {
        $setting = $this->getSettingByKey($key);
        if($setting) {
            $setting->setValue($value);
        } else {
            $setting = new Settings;
            $setting->setKey($key);

            if(is_array($value)) {
                $value = json_encode($value);
            }

            $setting->setValue(($value == "" && $value != 0) ? null : $value);
            $setting->setCreated();
            $setting->setModified();
            $setting->setUsersModified($user);
            $setting->setUsers($user);
            $this->settingsDao->save($setting);
        }
        
        $this->settingsDao->save($setting);
    }

    /**
     * Create setting array
     * @param null $user
     * @return array
     */
    public function getSettingsArray($user = null)
    {
        $return = [];
        foreach($this->getSettings($user) as $setting) {
            $return[$setting->getKey()] = $setting->getValue();
        }

        return $return;
    }

    /**
     * @param $values
     * @param $user
     */
    public function createSetting($values, $user)
    {
        foreach($values as $key => $value) {
            $setting = new Settings;
            $setting->setKey($key);
            $setting->setValue($value);
            $setting->setCreated();
            $setting->setModified();
            $setting->setUsersModified($user);
            $setting->setUsers($user);
            $this->settingsDao->save($setting);
        }
    }
}

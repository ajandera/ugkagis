<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\Model\Language\Manager;

use App\Model\Language\Entity\Language;
use App\Model\Users\Entity\Users;
use App\Model\Language\Storage\ILanguageDao;

/**
 * Class LanguageManager
 * @package App\Model\Language\Manager
 */
class LanguageManager
{
    /** @var ILanguageDao */
    private $languageDao;
    
    public function __construct(
        ILanguageDao $languageDao
    ) {
        $this->languageDao = $languageDao;
    }

    /**
     * @param array $condition
     * @param array $order
     * @param null $limit
     * @param null $offset
     * @return Language[]
     */
    public function getLanguages($condition = [], $order = [], $limit = null, $offset = null)
    {
        return $this->languageDao->findBy($condition, $order, $limit, $offset);
    }

    /**
     * @param $id
     * @return Language
     */
    public function getLanguageById($id)
    {
        return $this->languageDao->findById($id);
    }
    
    /**
     * @return Language
     */
    public function getDefaultLanguage()
    {
        return $this->languageDao->findOneBy(['defaults' => 1]);
    }
    
    /**
     * Delete language by id.
     * @param int $id
     */
    public function deleteLanguage($id)
    {
        $language = $this->getLanguageById($id);
        $this->languageDao->delete($language);
    }

    /**
     * Persist language to storage.
     * @param string $name
     * @param string $code
     * @param int $default
     * @param Users $user
     * @param $translationCode
     * @param $visible
     * @return Language
     */
    public function addLanguage($name, $code, $default, $user, $translationCode, $visible)
    {
        $language = new Language();
        $language->setName($name);
        $language->setCode($code);
        $language->setDefaults($default);
        $language->setCreated();
        $language->setModified();
        $language->setUsers($user);
        $language->setVisible($visible);
        $language->setUsersModified($user);
        $language->setTranslationCode($translationCode);
        $this->languageDao->save($language);
    
        if($default == 1) {
            $this->setDefaultLanguage($language->getId());
        }

        return $language;
    }

    /**
     * Upload language.
     * @param int $id
     * @param string $name
     * @param string $code
     * @param int $default
     * @param Users $user
     * @param $translationCode
     * @param $visible
     */
    public function updateLanguage($id, $name, $code, $default, $user, $translationCode, $visible)
    {
        $language = $this->getLanguageById($id);
        $language->setName($name);
        $language->setCode($code);
        $language->setDefaults($default);
        $language->setModified();
        $language->setUsersModified($user);
        $this->languageDao->save($language);
        $language->setTranslationCode($translationCode);
        $language->setVisible($visible);

        if($default == 1) {
            $this->setDefaultLanguage($language->getId());
        }
    }
    
    /**
     * Set default Language.
     * @param int $id
     */
    public function setDefaultLanguage($id)
    {
        foreach($this->getLanguages() as $language) {
            if($language->getId() == $id) {
                $language->setDefault(1);
                $this->languageDao->save($language);
            } else {
                $language->setDefault(0);
                $this->languageDao->save($language);
            }
        }
    }

    /**
     * @param string $code
     * @return Language
     */
    public function getLanguageByCode($code)
    {
        return $this->languageDao->findOneBy(['code' => $code]);
    }

    /**
     * @param $id
     */
    public function changeVisible($id)
    {
        $language = $this->getLanguageById($id);
        if($language->getVisible() == 0) {
            $language->setVisible(1);
        } else {
            $language->setVisible(0);
        }
        $this->languageDao->save($language);
    }
}

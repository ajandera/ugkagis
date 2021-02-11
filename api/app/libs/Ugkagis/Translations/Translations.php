<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\ePro\Translations;

class Translations
{

    /**
     * Set translation for language id
     * @param string $translation
     * @param int $language
     * @param array $translations
     * @return array
     */
    public static function setTranslation(string $translation, int $language, $translations) :string
    {
        $data = json_decode($translations);
        if (is_object($data)) {
            $data->$language = $translation;
        } else {
            $data[$language] = $translation;
        }
        return json_encode($data);
    }
    
    /**
     * Get translation for language id
     * @param int $language
     * @param array $translations
     * @return string
     */
    public static function getTranslation(int $language, string $translations) :string
    {
        $data = json_decode($translations);
        if (is_object($data)) {
            return isset($data->$language) ? $data->$language : '';
        } else {
            return isset($data[$language]) ? $data[$language] : '';
        }
    }
}

<?php
namespace Application\View\Helper;


class LanguageMenu
{
    private $languages;

    private $currentLanguageLabel;

    /**
     * @param array $lang
     */
    public function addLanguage($lang)
    {
        $this->languages[] = $lang;
    }

    /**
     * @return string
     */
    public function getCurrentLanguageLabel()
    {
        return $this->currentLanguageLabel;
    }

    /**
     * @param string $currentLanguageLabel
     */
    public function setCurrentLanguageLabel($currentLanguageLabel)
    {
        $this->currentLanguageLabel = $currentLanguageLabel;
    }

    /**
     * @return array
     */
    public function getLanguages()
    {
        return $this->languages;
    }

}

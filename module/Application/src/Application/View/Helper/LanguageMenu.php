<?php
namespace Application\View\Helper;


class LanguageMenu
{
    private $languages;

    private $currentLanguageLabel;

    /**
     * LanguageMenu constructor.
     * @param $currentLanguageLabel
     * @param array $menuLanguages
     */
    public function __construct($currentLanguageLabel, array $menuLanguages)
    {
        $this->currentLanguageLabel = $currentLanguageLabel;
        $this->languages = $menuLanguages;
    }

    /**
     * @return string
     */
    public function getCurrentLanguageLabel()
    {
        return $this->currentLanguageLabel;
    }

    /**
     * @return array
     */
    public function getLanguages()
    {
        return $this->languages;
    }

}

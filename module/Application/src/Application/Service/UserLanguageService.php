<?php

namespace Application\Service;

use Zend\Session\Container;

class UserLanguageService
{
    const SESSION_KEY = "user";
    const LANGUAGE = "lang";

    const DEFAULT_LOCALE = "it_IT";

    private $languages;

    public function __construct($languages)
    {
        $this->languages = $languages;
    }

    public function getCurrentLocale()
    {
        $container = new Container(UserLanguageService::SESSION_KEY);

        $locale = $container->offsetGet(UserLanguageService::LANGUAGE);

        foreach($this->languages as $lang) {
            if ($lang['locale'] == $locale) {
                return $lang['locale'];
            }
        }
        return self::DEFAULT_LOCALE;
    }
    public function getCurrentLang() {
        $locale = $this->getCurrentLocale();

        foreach($this->languages as $lang) {
            if ($lang['locale'] == $locale) {
                return $lang['lang'];
            }
        }
    }

    public function setCurrentLang($lang) {
        if (isset($this->languages[$lang])) {
            $container = new Container(UserLanguageService::SESSION_KEY);
            $locale = $this->languages[$lang]['locale'];
            $container->offsetSet(UserLanguageService::LANGUAGE, $locale);
        }
    }
}

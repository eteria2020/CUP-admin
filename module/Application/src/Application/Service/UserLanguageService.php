<?php

namespace Application\Service;

use Zend\Session\Container;

class UserLanguageService
{

    const SESSION_KEY = "user";
    const LANGUAGE = "lang";

    const DEFAULT_LANGUAGE = "it";

    public function getCurrentLang()
    {
        $container = new Container(UserLanguageService::SESSION_KEY);

        $lang = $container->offsetGet(UserLanguageService::LANGUAGE);
        if (!is_null($lang) && $lang != "") {
            return $lang;
        } else {
            return self::DEFAULT_LANGUAGE;
        }
    }

    public function setCurrentLang($lang) {
        $container = new Container(UserLanguageService::SESSION_KEY);
        $container->offsetSet(UserLanguageService::LANGUAGE, $lang);
    }


}

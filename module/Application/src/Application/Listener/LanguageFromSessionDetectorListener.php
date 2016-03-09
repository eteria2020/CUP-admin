<?php

namespace Application\Listener;

use MvLabsMultilanguage\LanguageRange\LanguageRange;
use MvLabsMultilanguage\Event\DetectLanguageEventInterface;
use MvLabsMultilanguage\Detector\Listener\LanguageDetectorListenerInterface;
use Zend\Session\Container;

class LanguageFromSessionDetectorListener implements LanguageDetectorListenerInterface
{
    const SESSION_KEY = "user";
    const LANGUAGE = "lang";
    const DEFAULT_LOCALE = "it_IT";
    /**
     * @inheritdoc
     */
    public function detectLanguage(DetectLanguageEventInterface $event)
    {
        $container = new Container(self::SESSION_KEY);

        //get session locale
        $locale = $container->offsetGet(self::LANGUAGE);

        if (is_null($locale)) {
            $languageRange = LanguageRange::fromString(self::DEFAULT_LOCALE);
            $event->removeLanguageRange($languageRange);
            $event->addLanguageRange($languageRange, 0);
        } else {
            $languageRange = LanguageRange::fromString($locale);
            $event->removeLanguageRange($languageRange);
            $event->addLanguageRange($languageRange, 1000);
        }

        return $event->getLanguageRanges();
    }
}

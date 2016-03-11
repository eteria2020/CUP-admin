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

        $locale = $container->offsetGet(self::LANGUAGE);

        if (is_null($locale)) {
            $locale = self::DEFAULT_LOCALE;
        }

        $languageRange = LanguageRange::fromString($locale);
        $event->removeLanguageRange($languageRange);
        $event->addLanguageRange($languageRange, 100);

        return $event->getLanguageRanges();
    }
}

<?php

namespace Application\Listener;

use Application\Service\UserLanguageService;
use MvLabsMultilanguage\LanguageRange\LanguageRange;
use MvLabsMultilanguage\Event\DetectLanguageEventInterface;

use MvLabsMultilanguage\Detector\Listener\LanguageDetectorListenerInterface;
use Zend\Session\Container;


class LanguageFromSessionDetectorListener implements LanguageDetectorListenerInterface
{
    /**
     * @inheritdoc
     */
    public function detectLanguage(DetectLanguageEventInterface $event)
    {
        $container = new Container(UserLanguageService::SESSION_KEY);

        //get session locale
        $locale = $container->offsetGet(UserLanguageService::LANGUAGE);

        if (!is_null($locale)) {

            $languageRange = LanguageRange::fromString($locale);
            $event->removeLanguageRange($languageRange);

            $event->addLanguageRange($languageRange, 1000);
        }

        //add default
        $languageRange = LanguageRange::fromString(UserLanguageService::DEFAULT_LOCALE);
        $event->removeLanguageRange($languageRange);

        $event->addLanguageRange($languageRange, 0);
        return $event->getLanguageRanges();
    }

}

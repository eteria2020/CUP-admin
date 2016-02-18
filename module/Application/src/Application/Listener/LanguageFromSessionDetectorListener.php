<?php

namespace Application\Listener;

use Application\Service\UserLanguageService;
use MvLabsMultilanguage\LanguageRange\LanguageRange;
use MvLabsMultilanguage\Event\DetectLanguageEventInterface;

use Zend\Http\Header\Accept\FieldValuePart\LanguageFieldValuePart;
use Zend\Mvc\Router\Http\Segment;
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

        //get session language
        $lang = $container->offsetGet(UserLanguageService::LANGUAGE);
        if (!is_null($lang) && $lang != "") {
            $languageRange = LanguageRange::fromString($lang);
            $priority = 100;

            $event->addLanguageRange($languageRange, $priority);
        }
//       //add default
        $languageRange = LanguageRange::fromString("it_IT");
        $priority = 1;

        $event->addLanguageRange($languageRange, $priority);


        return $event->getLanguageRanges();
    }

}

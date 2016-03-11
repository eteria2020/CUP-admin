<?php

namespace Application\Listener;

use MvLabsMultilanguage\LanguageRange\LanguageRange;
use MvLabsMultilanguage\Event\DetectLanguageEventInterface;
use MvLabsMultilanguage\Detector\Listener\LanguageDetectorListenerInterface;

use Zend\Session\Container;

class LanguageFromSessionDetectorListener implements LanguageDetectorListenerInterface
{

    private $params;

    public function __construct($params)
    {
        $this->params = $params;
    }

    public function detectLanguage(DetectLanguageEventInterface $event)
    {
        $container = new Container($this->params['languageSession']['session']);

        $locale = $container->offsetGet($this->params['languageSession']['offset']);

        if (is_null($locale)) {
            $locale = $this->params['translator']['locale'];
        }

        $languageRange = LanguageRange::fromString($locale);
        $event->removeLanguageRange($languageRange);
        $event->addLanguageRange($languageRange);

        return $event->getLanguageRanges();
    }
}

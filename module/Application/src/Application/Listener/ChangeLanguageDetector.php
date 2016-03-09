<?php

namespace Application\Listener;

use MvLabsMultilanguage\Service\LanguageService;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;

class ChangeLanguageDetector implements ListenerAggregateInterface
{
    const URL_PARAM = 'change-language';
    
    private $languageService;

    protected $listeners = array();

    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;
    }

    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     *
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'initChangeLanguageDetector'), 100);
    }

    /**
     * Detach all previously attached listeners
     *
     * @param EventManagerInterface $events
     *
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function initChangeLanguageDetector(MvcEvent $event)
    {
        //set changed language in session
        $request = $event->getRequest();
        $uri  = $request->getUri();
        $queryStringArray = $uri->getQueryAsArray();

        if (array_key_exists(self::URL_PARAM, $queryStringArray)) {
            $container = new Container(LanguageFromSessionDetectorListener::SESSION_KEY);
            $locale = $queryStringArray[self::URL_PARAM];
            $container->offsetSet(LanguageFromSessionDetectorListener::LANGUAGE, $locale);
        }
    }
}

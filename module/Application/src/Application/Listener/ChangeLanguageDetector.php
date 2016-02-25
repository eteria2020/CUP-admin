<?php

namespace Application\Listener;

use Application\Service\UserLanguageService;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;

class ChangeLanguageDetector implements ListenerAggregateInterface
{
    const GET_PARAMENTER_CHANGE_LANGUAGE = 'change-language';
    
    private $userLanguageService;

    protected $listeners = array();

    public function __construct(UserLanguageService $userLanguageService)
    {
        $this->userLanguageService = $userLanguageService;
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
        //set current language
        $request = $event->getRequest();
        $uri  = $request->getUri();
        $queryStringArray = $uri->getQueryAsArray();

        if (array_key_exists(self::GET_PARAMENTER_CHANGE_LANGUAGE, $queryStringArray)) {

           $this->userLanguageService->setCurrentLang($queryStringArray[self::GET_PARAMENTER_CHANGE_LANGUAGE]);
        }
    }

}

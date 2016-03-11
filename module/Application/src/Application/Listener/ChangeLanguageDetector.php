<?php

namespace Application\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;

class ChangeLanguageDetector implements ListenerAggregateInterface
{
    const URL_PARAM = 'lang';

    private $languageService;

    protected $listeners = array();

    private $params;

    public function __construct(LanguageService $languageService, array $params)
    {
        $this->languageService = $languageService;
        $this->params = $params;
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
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'initChangeLanguageDetector'), 200);
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
            $container = new Container($this->params['session']);
            $locale = $queryStringArray[self::URL_PARAM];
            $container->offsetSet($this->params['offset'], $locale);
        }
    }
}

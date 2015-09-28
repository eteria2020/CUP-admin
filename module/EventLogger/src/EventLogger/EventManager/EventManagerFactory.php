<?php

namespace EventLogger\EventManager;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\EventManager\EventManager;

class EventManagerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $eventManager = new EventManager();
        $eventManager->setIdentifiers(['EventLogger']);

        return $eventManager;
    }
}

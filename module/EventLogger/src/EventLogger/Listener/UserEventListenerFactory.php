<?php

namespace EventLogger\Listener;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UserEventListenerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $authService = $serviceLocator->get('zfcuser_auth_service');
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');

        return new UserEventListener($authService, $entityManager);
    }
}

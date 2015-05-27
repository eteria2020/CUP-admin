<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ConsoleUserControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $userService = $serviceLocator->getServiceLocator()->get('zfcuser_user_service');

        return new ConsoleUserController($userService);
    }
}

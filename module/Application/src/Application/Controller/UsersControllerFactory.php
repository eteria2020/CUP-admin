<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UsersControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $I_usersService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\UsersService');

        return new UsersController($I_usersService);
    }
}

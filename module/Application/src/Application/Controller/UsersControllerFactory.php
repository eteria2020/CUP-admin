<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UsersControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $I_usersService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\UsersService');
        $I_userForm = $serviceLocator->getServiceLocator()->get('UserForm');

        return new UsersController($I_usersService, $I_userForm);
    }
}

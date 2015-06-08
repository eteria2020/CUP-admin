<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class UsersControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $entityManager = $serviceLocator->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $I_usersService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\UsersService');
        $I_userForm = $serviceLocator->getServiceLocator()->get('UserForm');
        $hydrator = new DoctrineHydrator($entityManager);

        return new UsersController($I_usersService, $I_userForm, $hydrator);
    }
}

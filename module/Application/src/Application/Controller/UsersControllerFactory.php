<?php

namespace Application\Controller;

// Externals
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Session\Container;

class UsersControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedLocator = $serviceLocator->getServiceLocator();

        $entityManager = $sharedLocator->get('doctrine.entitymanager.orm_default');
        $usersService = $sharedLocator->get('SharengoCore\Service\UsersService');
        $userForm = $sharedLocator->get('UserForm');
        $hydrator = new DoctrineHydrator($entityManager);

        $datatablesSessionNamespace = $sharedLocator->get('Configuration')['session']['datatablesNamespace'];

        // Creating DataTable Filters Session Container
        $datatableFiltersSessionContainer = new Container($datatablesSessionNamespace);

        return new UsersController(
            $usersService,
            $userForm,
            $hydrator,
            $datatableFiltersSessionContainer
        );
    }
}

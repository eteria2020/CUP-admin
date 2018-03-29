<?php

namespace Application\Controller;

// Externals
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Session\Container;

class CarsControllerFactory implements FactoryInterface
{
    /**
     * Default method to be used in a Factory Class
     *
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedServiceLocator = $serviceLocator->getServiceLocator();

        $entityManager = $sharedServiceLocator->get('doctrine.entitymanager.orm_default');
        $carsService = $sharedServiceLocator->get('SharengoCore\Service\CarsService');
        $commandsService = $sharedServiceLocator->get('SharengoCore\Service\CommandsService');
        $datatablesSessionNamespace = $sharedServiceLocator->get('Configuration')['session']['datatablesNamespace'];

        $carForm = $sharedServiceLocator->get('CarForm');
        $hydrator = new DoctrineHydrator($entityManager);

        // Creating DataTable Filters Session Container
        $datatableFiltersSessionContainer = new Container($datatablesSessionNamespace);
        $authorize = $sharedServiceLocator->get('BjyAuthorize\Provider\Identity\ProviderInterface');
        $roles = $authorize->getIdentityRoles();

        return new CarsController(
            $carsService,
            $commandsService,
            $carForm,
            $hydrator,
            $datatableFiltersSessionContainer,
            $roles
        );
    }
}

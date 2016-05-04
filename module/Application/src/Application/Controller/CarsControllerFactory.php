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
        $sharedServiceLocator = $sharedServiceLocator;

        $entityManager = $sharedServiceLocator->get('doctrine.entitymanager.orm_default');
        $I_carsService = $sharedServiceLocator->get('SharengoCore\Service\CarsService');
        $I_commandsService = $sharedServiceLocator->get('SharengoCore\Service\CommandsService');
        $datatablesSessionNamespace = $sharedServiceLocator->get('Configuration')['session']['datatablesNamespace'];

        $I_carForm = $sharedServiceLocator->get('CarForm');
        $hydrator = new DoctrineHydrator($entityManager);

        // Creating DataTable Filters Session Container
        $datatableFiltersSessionContainer = new Container($datatablesSessionNamespace);

        return new CarsController(
            $I_carsService,
            $I_commandsService,
            $I_carForm,
            $hydrator,
            $datatableFiltersSessionContainer
        );
    }
}

<?php

namespace Application\Controller;

// Externals
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Session\Container;

class PoisControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedServiceLocator = $serviceLocator->getServiceLocator();

        $entityManager = $sharedServiceLocator->get('doctrine.entitymanager.orm_default');
        $poisService = $sharedServiceLocator->get('SharengoCore\Service\PoisService');
        $carsService = $sharedServiceLocator->get('SharengoCore\Service\CarsService');
        $datatablesSessionNamespace = $sharedServiceLocator->get('Configuration')['session']['datatablesNamespace'];

        $poiForm = $sharedServiceLocator->get('PoiForm');
        $hydrator = new DoctrineHydrator($entityManager);

        // Creating DataTable Filters Session Container
        $datatableFiltersSessionContainer = new Container($datatablesSessionNamespace);

        return new PoisController(
            $poisService,
            $carsService,
            $poiForm,
            $hydrator,
            $datatableFiltersSessionContainer
        );
    }
}

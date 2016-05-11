<?php

namespace Application\Controller;

// Externals
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Session\Container;

class ZonesControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedServiceLocator = $serviceLocator->getServiceLocator();

        $entityManager = $sharedServiceLocator->get('doctrine.entitymanager.orm_default');
        $zonesService = $sharedServiceLocator->get('SharengoCore\Service\ZonesService');
        $postGisService = $sharedServiceLocator->get('SharengoCore\Service\PostGisService');
        $datatablesSessionNamespace = $sharedServiceLocator->get('Configuration')['session']['datatablesNamespace'];

        $zoneForm = $sharedServiceLocator->get('ZoneForm');
        $hydrator = new DoctrineHydrator($entityManager);

        // Creating DataTable Filters Session Container
        $datatableFiltersSessionContainer = new Container($datatablesSessionNamespace);

        return new ZonesController(
            $zonesService,
            $postGisService,
            $zoneForm,
            $hydrator,
            $datatableFiltersSessionContainer
        );
    }
}

<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class ZonesControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $entityManager = $serviceLocator->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $zonesService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\ZonesService');
        $postGisService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\PostGisService');

        $zoneForm = $serviceLocator->getServiceLocator()->get('ZoneForm');
        $hydrator = new DoctrineHydrator($entityManager);

        return new ZonesController(
            $zonesService,
            $postGisService,
            $zoneForm,
            $hydrator
        );
    }
}

<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class PoisControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $entityManager = $serviceLocator->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $poisService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\PoisService');
        $carsService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\CarsService');

        $poiForm = $serviceLocator->getServiceLocator()->get('PoiForm');
        $hydrator = new DoctrineHydrator($entityManager);

        return new PoisController($poisService, $carsService, $poiForm, $hydrator);
    }
}

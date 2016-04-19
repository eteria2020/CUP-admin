<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

/**
 * Class ConfigurationsControllerFactory
 * @package Application\Controller
 */
class CarsConfigurationsControllerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CarsConfigurationsController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $entityManager = $serviceLocator->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $carsConfigurationsService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\CarsConfigurationsService');

        // Useful to fleets list
        $fleetService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\FleetService');

        // Static fields CarsConfigurations Form
        $carsConfigurationsForm = $serviceLocator->getServiceLocator()->get('CarsConfigurationsForm');

        $hydrator = new DoctrineHydrator($entityManager);

        return new CarsConfigurationsController($carsConfigurationsService, $fleetService, $carsConfigurationsForm, $hydrator);
    }
}

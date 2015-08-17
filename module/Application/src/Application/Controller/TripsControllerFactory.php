<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TripsControllerFactory implements FactoryInterface
{
    /**
     * Default method to be used in a Factory Class
     *
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedServiceLocator = $serviceLocator->getServiceLocator();

        // dependency is fetched from Service Manager
        $tripsService = $sharedServiceLocator->get('SharengoCore\Service\TripsService');
        $tripCostForm = $sharedServiceLocator->get('TripCostForm');
        $tripCostComputerService = $sharedServiceLocator->get('SharengoCore\Service\TripCostComputerService');

        // Controller is constructed, dependencies are injected (IoC in action)
        return new TripsController(
            $tripsService,
            $tripCostForm,
            $tripCostComputerService
        );
    }
}

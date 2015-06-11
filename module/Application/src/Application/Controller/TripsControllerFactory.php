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
        // dependency is fetched from Service Manager
        $I_tripsService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\TripsService');

        // Controller is constructed, dependencies are injected (IoC in action)
        return new TripsController($I_tripsService);
    }
}

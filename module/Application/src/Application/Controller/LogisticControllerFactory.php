<?php

namespace Application\Controller;

// Externals
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Session\Container;

class LogisticControllerFactory implements FactoryInterface {

    /**
     * Default method to be used in a Factory Class
     *
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator) {
        $sharedServiceLocator = $serviceLocator->getServiceLocator();

        $carsService =     $sharedServiceLocator->get('SharengoCore\Service\CarsService');
        $webusersService = $sharedServiceLocator->get('SharengoCore\Service\WebusersService');
        $maintenanceMotivationsService = $sharedServiceLocator->get('SharengoCore\Service\MaintenanceMotivationsService');
        $maintenanceLocationsService = $sharedServiceLocator->get('SharengoCore\Service\MaintenanceLocationsService');
        $config = $serviceLocator->getServiceLocator()->get('Config');

        return new LogisticController(
                $carsService, $webusersService, $maintenanceMotivationsService, $config['logistic'], $maintenanceLocationsService
        );
    }

}

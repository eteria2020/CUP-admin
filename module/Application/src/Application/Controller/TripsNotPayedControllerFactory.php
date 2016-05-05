<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container;

class TripsNotPayedControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedLocator = $serviceLocator->getServiceLocator();
        $tripsService = $sharedLocator->get('SharengoCore\Service\TripsService');

        // Creating DataTable Filters Session Container
        $datatablesSessionNamespace = $sharedLocator->get('Configuration')['session']['datatablesNamespace'];
        $datatableFiltersSessionContainer = new Container($datatablesSessionNamespace);

        return new TripsNotPayedController(
            $tripsService,
            $datatableFiltersSessionContainer
        );
    }
}

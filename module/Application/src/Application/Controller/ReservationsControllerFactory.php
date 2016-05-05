<?php

namespace Application\Controller;

// Externals
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container;

class ReservationsControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedServiceLocator = $serviceLocator->getServiceLocator();

        $reservationsService = $sharedServiceLocator->get('SharengoCore\Service\ReservationsService');
        $datatablesSessionNamespace = $sharedServiceLocator->get('Configuration')['session']['datatablesNamespace'];

        // Creating DataTable Filters Session Container
        $datatableFiltersSessionContainer = new Container($datatablesSessionNamespace);

        return new ReservationsController(
            $reservationsService,
            $datatableFiltersSessionContainer
        );
    }
}
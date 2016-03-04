<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ReservationsControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $reservationsService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\ReservationsService');

        return new ReservationsController($reservationsService);
    }
}
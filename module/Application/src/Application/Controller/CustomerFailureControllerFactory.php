<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CustomerFailureControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedLocator = $serviceLocator->getServiceLocator();

        $customersService = $sharedLocator->get('SharengoCore\Service\CustomersService');
        $tripPaymentsService = $sharedLocator->get('SharengoCore\Service\TripPaymentsService');
        $extraPaymentsService = $sharedLocator->get('SharengoCore\Service\ExtraPaymentsService');
        
        return new CustomerFailureController(
            $customersService,
            $tripPaymentsService,
            $extraPaymentsService
        );
    }
}

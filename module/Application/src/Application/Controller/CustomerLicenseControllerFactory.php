<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CustomerLicenseControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedLocator = $serviceLocator->getServiceLocator();

        $customersService = $sharedLocator->get('SharengoCore\Service\CustomersService');
        $validationService = $sharedLocator->get('SharengoCore\Service\DriversLicenseValidationService');
        $deactivationService = $sharedLocator->get('SharengoCore\Service\CustomerDeactivationService');

        return new CustomerLicenseController(
            $customersService,
            $validationService,
            $deactivationService
        );
    }
}

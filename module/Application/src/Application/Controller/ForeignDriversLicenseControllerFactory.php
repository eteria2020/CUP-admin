<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ForeignDriversLicenseControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedLocator = $serviceLocator->getServiceLocator();

        $ForeignDriversLicenseService = $sharedLocator->get('SharengoCore\Service\ForeignDriversLicenseService');
        $validateForeignDriversLicenseService = $sharedLocator->get('SharengoCore\Service\ValidateForeignDriversLicenseService');

        return new ForeignDriversLicenseController(
            $ForeignDriversLicenseService,
            $validateForeignDriversLicenseService
        );
    }
}

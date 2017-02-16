<?php

namespace Application\Controller;

// Externals
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container;

class ForeignDriversLicenseControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedLocator = $serviceLocator->getServiceLocator();

        $ForeignDriversLicenseService = $sharedLocator->get('SharengoCore\Service\ForeignDriversLicenseService');
        $validateForeignDriversLicenseService = $sharedLocator->get('SharengoCore\Service\ValidateForeignDriversLicenseService');
        $datatablesSessionNamespace = $sharedLocator->get('Configuration')['session']['datatablesNamespace'];

        // Creating DataTable Filters Session Container
        $datatableFiltersSessionContainer = new Container($datatablesSessionNamespace);

        return new ForeignDriversLicenseController(
            $ForeignDriversLicenseService,
            $validateForeignDriversLicenseService,
            $datatableFiltersSessionContainer
        );
    }
}

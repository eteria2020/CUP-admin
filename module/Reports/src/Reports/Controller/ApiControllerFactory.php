<?php

namespace Reports\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ApiControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $reportsService = $serviceLocator->getServiceLocator()->get('Reports\Service\Reports');
        $reportsCsvService = $serviceLocator->getServiceLocator()->get('Reports\Service\ReportsCsvService');

        return new ApiController($reportsService, $reportsCsvService);
    }
}

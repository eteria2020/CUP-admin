<?php

namespace Reports\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ReportsCsvServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $reportsService = $serviceLocator->get('Reports\Service\Reports');

        return new ReportsCsvService($reportsService);
    }
}

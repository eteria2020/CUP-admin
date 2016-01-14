<?php

namespace Reports\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ReportsCsvServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $reportsService = $serviceLocator->getServiceLocator()->get('Reports\Service\Reports');
        $database = $serviceLocator->get('doctrine.connection.orm_default');

        return new ReportsCsvService($reportsService,$database);
    }
}

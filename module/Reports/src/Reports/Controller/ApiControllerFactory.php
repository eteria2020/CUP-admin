<?php

namespace Reports\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ApiControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $reportsService = $serviceLocator->getServiceLocator()->get('Reports\Service\Reports');

        return new ApiController($reportsService);
    }
}

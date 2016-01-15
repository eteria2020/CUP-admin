<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PaymentsCsvControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedServiceManager = $serviceLocator->getServiceLocator();
        $csvService = $sharedServiceManager->get('SharengoCore\Service\CsvService');

        return new PaymentsCsvController(
            $csvService
        );
    }
}

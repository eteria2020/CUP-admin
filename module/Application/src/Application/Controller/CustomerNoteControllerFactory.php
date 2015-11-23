<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CustomerNoteControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedLocator = $serviceLocator->getServiceLocator();

        $customersService = $sharedLocator->get('SharengoCore\Service\CustomersService');
        $customerNoteService = $sharedLocator->get('SharengoCore\Service\CustomerNoteService');

        return new CustomerNoteController(
            $customersService,
            $customerNoteService
        );
    }
}

<?php

namespace Application\Controller;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CustomersEditControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedLocator = $serviceLocator->getServiceLocator();

        $customersService = $sharedLocator->get('SharengoCore\Service\CustomersService');
        $deactivationService = $sharedLocator->get('SharengoCore\Service\CustomerDeactivationService');
        $tripPaymentTriesService = $sharedLocator->get('SharengoCore\Service\TripPaymentTriesService');
        $entityManager = $sharedLocator->get('doctrine.entitymanager.orm_default');
        $hydrator = new DoctrineHydrator($entityManager);
        $customerForm = $sharedLocator->get('CustomerForm');
        $driverForm = $sharedLocator->get('DriverForm');
        $settingForm = $sharedLocator->get('SettingForm');
        $config = $sharedLocator->get('Config');

        return new CustomersEditController(
            $customersService,
            $deactivationService,
            $tripPaymentTriesService,
            $hydrator,
            $customerForm,
            $driverForm,
            $settingForm,
            $config
        );
    }
}

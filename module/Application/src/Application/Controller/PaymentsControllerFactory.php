<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PaymentsControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedServiceManager = $serviceLocator->getServiceLocator();

        $tripPaymentsService = $sharedServiceManager->get('SharengoCore\Service\TripPaymentsService');
        $paymentsService = $sharedServiceManager->get('SharengoCore\Service\PaymentsService');
        $customersService = $sharedServiceManager->get('SharengoCore\Service\CustomersService');
        $extraPaymentsForm = $sharedServiceManager->get('ExtraPaymentsForm');
        $cartasiContractsService = $sharedServiceManager->get('Cartasi\Service\CartasiContracts');
        $cartasiCustomerPayments = $sharedServiceManager->get('Cartasi\Service\CartasiCustomerPayments');
        $extraPaymentsService = $sharedServiceManager->get('SharengoCore\Service\ExtraPaymentsService');
        $penaltiesService = $sharedServiceManager->get('SharengoCore\Service\PenaltiesService');
        $fleetService = $sharedServiceManager->get('SharengoCore\Service\FleetService');
        $recapService = $sharedServiceManager->get('SharengoCore\Service\RecapService');

        return new PaymentsController(
            $tripPaymentsService,
            $paymentsService,
            $customersService,
            $extraPaymentsForm,
            $cartasiContractsService,
            $cartasiCustomerPayments,
            $extraPaymentsService,
            $penaltiesService,
            $fleetService,
            $recapService
        );
    }
}

<?php

namespace Application\Controller;

// Externals
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container;

class PaymentsControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedServiceManager = $serviceLocator->getServiceLocator();

        $tripPaymentsService = $sharedServiceManager->get('SharengoCore\Service\TripPaymentsService');
        $paymentsService = $sharedServiceManager->get('SharengoCore\Service\PaymentsService');
        $customersService = $sharedServiceManager->get('SharengoCore\Service\CustomersService');
        $cartasiContractsService = $sharedServiceManager->get('Cartasi\Service\CartasiContracts');
        $cartasiCustomerPayments = $sharedServiceManager->get('Cartasi\Service\CartasiCustomerPayments');
        $extraPaymentsService = $sharedServiceManager->get('SharengoCore\Service\ExtraPaymentsService');
        $extraPaymentTriesService = $sharedServiceManager->get('SharengoCore\Service\ExtraPaymentTriesService');
        $deactivationService = $sharedServiceManager->get('SharengoCore\Service\CustomerDeactivationService');
        $penaltiesService = $sharedServiceManager->get('SharengoCore\Service\PenaltiesService');
        $fleetService = $sharedServiceManager->get('SharengoCore\Service\FleetService');
        $recapService = $sharedServiceManager->get('SharengoCore\Service\RecapService');
        $faresService = $sharedServiceManager->get('SharengoCore\Service\FaresService');
        $faresForm = $sharedServiceManager->get('FaresForm');
        $datatablesSessionNamespace = $sharedServiceManager->get('Configuration')['session']['datatablesNamespace'];
        

        // Creating DataTable Filters Session Container
        $datatableFiltersSessionContainer = new Container($datatablesSessionNamespace);

        return new PaymentsController(
            $tripPaymentsService,
            $paymentsService,
            $customersService,
            $cartasiContractsService,
            $cartasiCustomerPayments,
            $extraPaymentsService,
            $extraPaymentTriesService,
            $penaltiesService,
            $fleetService,
            $recapService,
            $faresService,
            $faresForm,
            $datatableFiltersSessionContainer,
            $deactivationService
        );
    }
}

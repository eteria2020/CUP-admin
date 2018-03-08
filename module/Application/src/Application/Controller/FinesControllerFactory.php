<?php

namespace Application\Controller;

// Externals
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container;

class FinesControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedServiceManager = $serviceLocator->getServiceLocator();

        $finesService = $sharedServiceManager->get('SharengoCore\Service\FinesService');
        $paymentsService = $sharedServiceManager->get('SharengoCore\Service\PaymentsService');
        $customersService = $sharedServiceManager->get('SharengoCore\Service\CustomersService');
        $cartasiContractsService = $sharedServiceManager->get('Cartasi\Service\CartasiContracts');
        $cartasiCustomerPayments = $sharedServiceManager->get('Cartasi\Service\CartasiCustomerPayments');
        $extraPaymentsService = $sharedServiceManager->get('SharengoCore\Service\ExtraPaymentsService');
        $penaltiesService = $sharedServiceManager->get('SharengoCore\Service\PenaltiesService');
        $fleetService = $sharedServiceManager->get('SharengoCore\Service\FleetService');
        $recapService = $sharedServiceManager->get('SharengoCore\Service\RecapService');
        $faresService = $sharedServiceManager->get('SharengoCore\Service\FaresService');
        $faresForm = $sharedServiceManager->get('FaresForm');
        $datatablesSessionNamespace = $sharedServiceManager->get('Configuration')['session']['datatablesNamespace'];

        // Creating DataTable Filters Session Container
        $datatableFiltersSessionContainer = new Container($datatablesSessionNamespace);

        return new FinesController(
            $finesService,
            $paymentsService,
            $customersService,
            $cartasiContractsService,
            $cartasiCustomerPayments,
            $extraPaymentsService,
            $penaltiesService,
            $fleetService,
            $recapService,
            $faresService,
            $faresForm,
            $datatableFiltersSessionContainer
        );
    }
}

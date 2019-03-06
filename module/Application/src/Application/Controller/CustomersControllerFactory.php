<?php

namespace Application\Controller;

// Externals
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Session\Container;
use SharengoCore\Entity\Configurations;

class CustomersControllerFactory implements FactoryInterface
{
    /**
     * Default method to be used in a Factory Class
     *
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     * @param ServiceLocatorInterface $serviceLocator
     * @return CustomersController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedLocator = $serviceLocator->getServiceLocator();

        // dependency is fetched from Service Manager
        $entityManager = $sharedLocator->get('doctrine.entitymanager.orm_default');
        $clientService = $sharedLocator->get('SharengoCore\Service\CustomersService');
        $customerDeactivationService = $sharedLocator->get('SharengoCore\Service\CustomerDeactivationService');
        $oldCustomerDiscountsService = $sharedLocator->get('SharengoCore\Service\OldCustomerDiscountsService');
        $emailService = $sharedLocator->get('\SharengoCore\Service\EmailService');
        $cardsService = $sharedLocator->get('SharengoCore\Service\CardsService');
        $promoCodeService = $sharedLocator->get('SharengoCore\Service\PromoCodesService');
        $bonusService = $sharedLocator->get('SharengoCore\Service\BonusService');
        $userEventsService = $sharedLocator->get('SharengoCore\Service\UserEventsService');
        $pointService = $sharedLocator->get('SharengoCore\Service\PointService');
        $customerForm = $sharedLocator->get('CustomerForm');
        $driverForm = $sharedLocator->get('DriverForm');
        $settingForm = $sharedLocator->get('SettingForm');
        $promoCodeForm = $sharedLocator->get('PromoCodeForm');
        $customerBonusForm = $sharedLocator->get('CustomerBonusForm');
        $customerPointForm = $sharedLocator->get('CustomerPointForm');
        $cardForm = $sharedLocator->get('CardForm');
        $datatablesSessionNamespace = $sharedLocator->get('Configuration')['session']['datatablesNamespace'];
        $config = $sharedLocator->get('Config');
        $globalConfig = $config['emailSettings'];
        
        $hydrator = new DoctrineHydrator($entityManager);

        $cartasiContractsService = $sharedLocator->get('Cartasi\Service\CartasiContracts');
        $disableContractService = $sharedLocator->get('SharengoCore\Service\DisableContractService');

        // Creating DataTable Filters Session Container
        $datatableFiltersSessionContainer = new Container($datatablesSessionNamespace);
        
        $registrationService = $sharedLocator->get('RegistrationService');

        $authorize = $sharedLocator->get('BjyAuthorize\Provider\Identity\ProviderInterface');
        $roles = $authorize->getIdentityRoles();

        $configurationService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\ConfigurationsService');
        $silverConfig = $configurationService->getConfigurationsKeyValueBySlug(Configurations::SILVER);

        // Controller is constructed, dependencies are injected (IoC in action)
        return new CustomersController(
            $roles,
            $clientService,
            $customerDeactivationService,
            $cardsService,
            $promoCodeService,
            $bonusService,
            $pointService,
            $customerForm,
            $driverForm,
            $settingForm,
            $promoCodeForm,
            $customerBonusForm,
            $customerPointForm,
            $cardForm,
            $hydrator,
            $cartasiContractsService,
            $disableContractService,
            $datatableFiltersSessionContainer,
            $registrationService,
            $emailService,
            $userEventsService,
            $globalConfig,
            $oldCustomerDiscountsService,
            $silverConfig
        );
    }
}

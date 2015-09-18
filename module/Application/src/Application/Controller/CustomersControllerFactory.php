<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class CustomersControllerFactory implements FactoryInterface
{
    /**
     * Default method to be used in a Factory Class
     *
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedLocator = $serviceLocator->getServiceLocator();

        // dependency is fetched from Service Manager
        $entityManager = $sharedLocator->get('doctrine.entitymanager.orm_default');
        $I_clientService = $sharedLocator->get('SharengoCore\Service\CustomersService');
        $I_cardsService = $sharedLocator->get('SharengoCore\Service\CardsService');
        $I_promoCodeService = $sharedLocator->get('SharengoCore\Service\PromoCodesService');
        $I_customerForm = $sharedLocator->get('CustomerForm');
        $I_driverForm = $sharedLocator->get('DriverForm');
        $I_settingForm = $sharedLocator->get('SettingForm');
        $I_promoCodeForm = $sharedLocator->get('PromoCodeForm');
        $I_customerBonusForm = $sharedLocator->get('CustomerBonusForm');

        $hydrator = new DoctrineHydrator($entityManager);

        $cartasiContractsService = $sharedLocator->get('Cartasi\Service\CartasiContracts');

        // Controller is constructed, dependencies are injected (IoC in action)
        return new CustomersController(
            $I_clientService,
            $I_cardsService,
            $I_promoCodeService,
            $I_customerForm,
            $I_driverForm,
            $I_settingForm,
            $I_promoCodeForm,
            $I_customerBonusForm,
            $hydrator,
            $cartasiContractsService
        );
    }
}

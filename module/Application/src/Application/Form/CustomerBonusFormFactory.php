<?php

namespace Application\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class CustomerBonusFormFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CustomerBonusForm
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $hydrator = new DoctrineHydrator($entityManager);
        $addBonusService = $serviceLocator->get('SharengoCore\Service\AddBonusService');
        $languageService = $serviceLocator->get('LanguageService');
        $translator = $languageService->getTranslator();
        $config = $serviceLocator->get('Config');

        $authorize = $serviceLocator->get('BjyAuthorize\Provider\Identity\ProviderInterface');
        $roles = $authorize->getIdentityRoles();
        
        $customerBonusFieldset = new CustomerBonusFieldset($hydrator, $translator, $addBonusService, $config, $roles);

        return new CustomerBonusForm($customerBonusFieldset);
    }
}
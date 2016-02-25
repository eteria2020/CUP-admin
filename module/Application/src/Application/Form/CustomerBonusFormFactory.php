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

        $languageService = $serviceLocator->get('LanguageService');
        $translator = $languageService->getTranslator();

        $customerBonusFieldset = new CustomerBonusFieldset($hydrator, $translator);

        return new CustomerBonusForm($customerBonusFieldset);
    }
}
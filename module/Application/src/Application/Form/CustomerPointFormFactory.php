<?php

namespace Application\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class CustomerPointFormFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CustomerPointForm
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $hydrator = new DoctrineHydrator($entityManager);
        $addPointService = $serviceLocator->get('SharengoCore\Service\AddPointService');
        $languageService = $serviceLocator->get('LanguageService');
        $translator = $languageService->getTranslator();

        $customerPointFieldset = new CustomerPointFieldset($hydrator, $translator, $addPointService);

        return new CustomerPointForm($customerPointFieldset);
    }
}
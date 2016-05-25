<?php

namespace Application\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class ZoneFormFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ZoneForm
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');

        $languageService = $serviceLocator->get('LanguageService');
        $translator = $languageService->getTranslator();

        $hydrator = new DoctrineHydrator($entityManager);
        $zoneFieldset = new ZoneFieldset(
            $hydrator,
            $translator
        );

        return new ZoneForm($zoneFieldset, $entityManager);
    }
}

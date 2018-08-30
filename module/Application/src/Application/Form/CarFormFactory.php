<?php

namespace Application\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class CarFormFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CarForm
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $I_carsService = $serviceLocator->get('SharengoCore\Service\CarsService');


        $languageService = $serviceLocator->get('LanguageService');
        $maintenanceMotivationsService = $serviceLocator->get('SharengoCore\Service\MaintenanceMotivationsService');
        $maintenanceLocationsService = $serviceLocator->get('SharengoCore\Service\MaintenanceLocationsService');
        $translator = $languageService->getTranslator();

        $hydrator = new DoctrineHydrator($entityManager);
        $carFieldset = new CarFieldset($I_carsService, $hydrator, $translator);

        return new CarForm($carFieldset, $maintenanceMotivationsService, $maintenanceLocationsService);
    }
}
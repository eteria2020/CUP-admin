<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

/**
 * Class ConfigurationsControllerFactory
 * @package Application\Controller
 */
class CarsConfigurationsControllerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CarsConfigurationsController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedServiceLocator = $serviceLocator->getServiceLocator();

        $entityManager = $sharedServiceLocator->get('doctrine.entitymanager.orm_default');
        $carsConfigurationsService = $sharedServiceLocator->get('SharengoCore\Service\CarsConfigurationsService');
        $translator = $sharedServiceLocator->get('MvcTranslator');

        // Useful to fleets list
        $fleetService = $sharedServiceLocator->get('SharengoCore\Service\FleetService');

        // Static fields CarsConfigurations Form
        $carsConfigurationsForm = $sharedServiceLocator->get('CarsConfigurationsForm');

        $hydrator = new DoctrineHydrator($entityManager);

        return new CarsConfigurationsController(
            $carsConfigurationsService,
            $fleetService,
            $carsConfigurationsForm,
            $hydrator,
            $translator
        );
    }
}

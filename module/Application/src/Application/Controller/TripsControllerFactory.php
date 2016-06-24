<?php

namespace Application\Controller;

use Application\Form\InputData\CloseTripDataFactory;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container;

class TripsControllerFactory implements FactoryInterface
{
    /**
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedServiceLocator = $serviceLocator->getServiceLocator();

        $tripsService = $sharedServiceLocator->get('SharengoCore\Service\TripsService');
        $tripCostForm = $sharedServiceLocator->get('TripCostForm');
        $tripCostComputerService = $sharedServiceLocator->get('SharengoCore\Service\TripCostComputerService');
        $eventsService = $sharedServiceLocator->get('SharengoCore\Service\EventsService');
        $entityManager = $sharedServiceLocator->get('doctrine.entitymanager.orm_default');
        $tripsRepository = $entityManager->getRepository('SharengoCore\Entity\Trips');
        $datatablesSessionNamespace = $sharedServiceLocator->get('Configuration')['session']['datatablesNamespace'];

        $languageService = $sharedServiceLocator->get('LanguageService');
        $translator = $languageService->getTranslator();

        $closeTripDataFactory = new CloseTripDataFactory($tripsRepository, $translator);

        // Creating DataTable Filters Session Container
        $datatableFiltersSessionContainer = new Container($datatablesSessionNamespace);

        return new TripsController(
            $tripsService,
            $tripCostForm,
            $tripCostComputerService,
            $eventsService,
            $closeTripDataFactory,
            $datatableFiltersSessionContainer
        );
    }
}

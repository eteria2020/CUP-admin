<?php

namespace Application\Controller;

use Application\Form\InputData\CloseTripDataFactory;

//use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container;

class FinesControllerFactory implements FactoryInterface
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
        $logsService = $sharedServiceLocator->get('SharengoCore\Service\LogsService');
        $entityManager = $sharedServiceLocator->get('doctrine.entitymanager.orm_default');
        $tripsRepository = $entityManager->getRepository('SharengoCore\Entity\Trips');
        $datatablesSessionNamespace = $sharedServiceLocator->get('Configuration')['session']['datatablesNamespace'];
        $eventManager = $sharedServiceLocator->get('EventLogger\EventManager\EventManager');
        $businessService = $sharedServiceLocator->get('BusinessCore\Service\BusinessService');
        $businessTripService = $sharedServiceLocator->get('BusinessCore\Service\BusinessTripService');

        $languageService = $sharedServiceLocator->get('LanguageService');
        $translator = $languageService->getTranslator();

        $closeTripDataFactory = new CloseTripDataFactory($tripsRepository, $translator);

        // Creating DataTable Filters Session Container
        $datatableFiltersSessionContainer = new Container($datatablesSessionNamespace);

        return new FinesController(
            $tripsService,
            $tripCostForm,
            $tripCostComputerService,
            $eventsService,
            $logsService,
            $eventManager,
            $closeTripDataFactory,
            $datatableFiltersSessionContainer,
            $businessService,
            $businessTripService
        );
    }
}

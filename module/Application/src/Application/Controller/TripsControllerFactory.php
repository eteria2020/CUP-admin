<?php

namespace Application\Controller;

use Application\Form\InputData\CloseTripDataFactory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class TripsControllerFactory implements FactoryInterface
{
    /**
     * Default method to be used in a Factory Class
     *
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedServiceLocator = $serviceLocator->getServiceLocator();

        // dependency is fetched from Service Manager
        $tripsService = $sharedServiceLocator->get('SharengoCore\Service\TripsService');
        $tripCostForm = $sharedServiceLocator->get('TripCostForm');
        $tripCostComputerService = $sharedServiceLocator->get('SharengoCore\Service\TripCostComputerService');
        $eventsService = $sharedServiceLocator->get('SharengoCore\Service\EventsService');
        $editTripsService = $sharedServiceLocator->get('SharengoCore\Service\EditTripsService');
        $editTripForm = $sharedServiceLocator->get('EditTripForm');
        $eventManager = $sharedServiceLocator->get('EventLogger\EventManager\EventManager');
        $entityManager = $sharedServiceLocator->get('doctrine.entitymanager.orm_default');
        $hydrator = new DoctrineHydrator($entityManager);
        $tripsRepository = $entityManager->getRepository('SharengoCore\Entity\Trips');
        $closeTripDataFactory = new CloseTripDataFactory($tripsRepository);

        // Controller is constructed, dependencies are injected (IoC in action)
        return new TripsController(
            $tripsService,
            $tripCostForm,
            $tripCostComputerService,
            $eventsService,
            $editTripsService,
            $editTripForm,
            $eventManager,
            $hydrator,
            $closeTripDataFactory
        );
    }
}

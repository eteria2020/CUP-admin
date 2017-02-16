<?php

namespace Application\Controller;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EditTripControllerFactory implements FactoryInterface
{
    /**
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedServiceLocator = $serviceLocator->getServiceLocator();

        $tripsService = $sharedServiceLocator->get('SharengoCore\Service\TripsService');
        $eventsService = $sharedServiceLocator->get('SharengoCore\Service\EventsService');
        $editTripsService = $sharedServiceLocator->get('SharengoCore\Service\EditTripsService');
        $editTripForm = $sharedServiceLocator->get('EditTripForm');
        $eventManager = $sharedServiceLocator->get('EventLogger\EventManager\EventManager');
        $entityManager = $sharedServiceLocator->get('doctrine.entitymanager.orm_default');
        $hydrator = new DoctrineHydrator($entityManager);
        $paymentScriptRunsService = $sharedServiceLocator->get('SharengoCore\Service\PaymentScriptRunsService');

        return new EditTripController(
            $tripsService,
            $editTripsService,
            $eventManager,
            $eventsService,
            $hydrator,
            $editTripForm,
            $paymentScriptRunsService
        );
    }
}

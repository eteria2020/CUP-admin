<?php

namespace Application\Controller;

// Externals
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container;

class NotificationsControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedServiceLocator = $serviceLocator->getServiceLocator();

        $entityManager = $sharedServiceLocator->get('doctrine.entitymanager.orm_default');
        $notificationsService = $sharedServiceLocator->get('SharengoCore\Service\NotificationsService');
        $notificationsProtocolsService = $sharedServiceLocator->get('SharengoCore\Service\NotificationsProtocolsService');
        $notificationsCategoriesService = $sharedServiceLocator->get('SharengoCore\Service\NotificationsCategoriesService');
        $datatablesSessionNamespace = $sharedServiceLocator->get('Configuration')['session']['datatablesNamespace'];

        $notificationsCategoriesAbstractFactory = $sharedServiceLocator->get('SharengoCore\Service\NotificationsCategories\NotificationsCategoriesAbstractFactory');

        // Creating DataTable Filters Session Container
        $datatableFiltersSessionContainer = new Container($datatablesSessionNamespace);

        return new NotificationsController(
            $notificationsService,
            $notificationsProtocolsService,
            $notificationsCategoriesService,
            $notificationsCategoriesAbstractFactory,
            $datatableFiltersSessionContainer
        );
    }
}

<?php

namespace Application\View\Helper\Notifications;

// Externals
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SOSCategoryFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return SOSCategory
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedLocator = $serviceLocator->getServiceLocator();

        $customersService = $sharedLocator->get('SharengoCore\Service\CustomersService');
        $tripService = $sharedLocator->get('SharengoCore\Service\TripsService');

        return new SOSCategory(
            $customersService,
            $tripService
        );
    }
}

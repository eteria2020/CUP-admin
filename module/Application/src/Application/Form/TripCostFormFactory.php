<?php

namespace Application\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class TripCostFormFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return TripCostForm
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /*$entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $hydrator = new DoctrineHydrator($entityManager);
        $I_clientService = $serviceLocator->get('SharengoCore\Service\CustomersService');
        $countriesService = $serviceLocator->get('SharengoCore\Service\CountriesService');
        $provincesService = $serviceLocator->get('SharengoCore\Service\ProvincesService');*/
        $tripCostFieldset = new TripCostFieldset(
            /*$I_clientService,
            $countriesService,
            $provincesService,
            $hydrator*/
        );

        return new TripCostForm($tripCostFieldset);
    }
}

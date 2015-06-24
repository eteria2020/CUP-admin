<?php

namespace Application\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class DriverFormFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return UserForm
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $I_countriesService = $serviceLocator->get('SharengoCore\Service\CountriesService');
        $I_authorityService = $serviceLocator->get('SharengoCore\Service\AuthorityService');

        $hydrator = new DoctrineHydrator($entityManager);
        $driverFieldset = new DriverFieldset($I_countriesService, $I_authorityService, $hydrator);

        return new DriverForm($driverFieldset);
    }
}

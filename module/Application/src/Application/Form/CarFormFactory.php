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
        $hydrator = new DoctrineHydrator($entityManager);
        $carFieldset = new CarFieldset($I_carsService, $hydrator);

        return new CarForm($carFieldset);
    }
}
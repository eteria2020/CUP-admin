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
        $tripCostFieldset = new TripCostFieldset();

        return new TripCostForm($tripCostFieldset);
    }
}

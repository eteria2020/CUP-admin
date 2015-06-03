<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Hydrator\Reflection;

class CarsControllerFactory implements FactoryInterface
{
    /**
     * Default method to be used in a Factory Class
     *
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $I_carsService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\CarsService');
        $I_carForm = $serviceLocator->getServiceLocator()->get('CarForm');
        $hydrator = new Reflection();

        return new CarsController($I_carsService, $I_carForm, $hydrator);
    }
}
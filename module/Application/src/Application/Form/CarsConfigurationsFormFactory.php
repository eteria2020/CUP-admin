<?php

namespace Application\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class CarsConfigurationsFormFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CarsConfigurationsForm
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $translator = $serviceLocator->get('MvcTranslator');

        $hydrator = new DoctrineHydrator($entityManager);
        $carsConfigurationsFieldset = new CarsConfigurationsFieldset($translator, $hydrator);

        return new CarsConfigurationsForm($carsConfigurationsFieldset);
    }
}

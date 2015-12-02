<?php

namespace Application\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class PoiFormFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return PoiForm
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $poisService = $serviceLocator->get('SharengoCore\Service\PoisService');
        $hydrator = new DoctrineHydrator($entityManager);
        $poiFieldset = new PoiFieldset($poisService, $hydrator);

        return new PoiForm($poiFieldset);
    }
}

<?php

namespace Application\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class ExtraPaymentsFormFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ExtraPaymentsForm
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $fleetService = $serviceLocator->get('SharengoCore\Service\FleetService');
        $fieldset = new ExtraPaymentsFieldset($fleetService);

        return new ExtraPaymentsForm($fieldset);
    }
}

<?php

namespace Application\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EditTripFormFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return EditTripForm
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $fieldset = new EditTripFieldset();

        return new EditTripForm($fieldset);
    }
}

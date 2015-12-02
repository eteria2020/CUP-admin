<?php

namespace Application\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FaresFormFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return FaresForm
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $faresService = $serviceLocator->get('SharengoCore\Service\FaresService');
        $fieldset = new FaresFieldset($faresService);

        return new FaresForm($fieldset);
    }
}

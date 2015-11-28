<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ConfigurationsControllerFactory
 * @package Application\Controller
 */
class ConfigurationsControllerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ConfigurationsController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $I_configurationsService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\ConfigurationsService');
        $I_configurationsForm = $serviceLocator->getServiceLocator()->get('ConfigurationsForm');

        return new ConfigurationsController($I_configurationsService, $I_configurationsForm);
    }
}

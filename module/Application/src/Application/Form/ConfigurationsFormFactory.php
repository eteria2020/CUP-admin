<?php

namespace Application\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

/**
 * Class ConfigurationsFormFactory
 * @package Application\Form
 */
class ConfigurationsFormFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ConfigurationsForm
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $I_configurationsService = $serviceLocator->get('SharengoCore\Service\ConfigurationsService');
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $hydrator = new DoctrineHydrator($entityManager);

        return new ConfigurationsForm($I_configurationsService, $hydrator);
    }
}
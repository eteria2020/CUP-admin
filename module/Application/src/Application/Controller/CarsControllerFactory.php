<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class CarsControllerFactory implements FactoryInterface
{
    /**
     * Default method to be used in a Factory Class
     *
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $entityManager = $serviceLocator->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $I_carsService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\CarsService');
        $I_commandsService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\CommandsService');

        $languageService = $serviceLocator->getServiceLocator()->get('LanguageService');
        $translator = $languageService->getTranslator();

        $I_carForm = $serviceLocator->getServiceLocator()->get('CarForm');
        $hydrator = new DoctrineHydrator($entityManager);

        return new CarsController($I_carsService, $I_commandsService, $I_carForm, $hydrator, $translator);
    }
}

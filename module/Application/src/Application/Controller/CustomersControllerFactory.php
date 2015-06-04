<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class CustomersControllerFactory implements FactoryInterface
{
    /**
     * Default method to be used in a Factory Class
     *
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        // dependency is fetched from Service Manager
        $entityManager = $serviceLocator->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $I_clientService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\CustomersService');
        $I_customerForm = $serviceLocator->getServiceLocator()->get('CustomerForm');
        $hydrator = new DoctrineHydrator($entityManager);

        // Controller is constructed, dependencies are injected (IoC in action)
        return new CustomersController($I_clientService, $I_customerForm, $hydrator);
    }
}

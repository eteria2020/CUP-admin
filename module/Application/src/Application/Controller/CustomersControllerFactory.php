<?php

namespace Application\Controller;

use Application\Form\CustomerForm;
use Application\Form\CustomerFormFilter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Hydrator\Reflection;

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
        $I_clientService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\CustomersService');
        $I_customerForm = $serviceLocator->getServiceLocator()->get('CustomerForm');
        $hydrator = new Reflection();

        // Controller is constructed, dependencies are injected (IoC in action)
        return new CustomersController($I_clientService, $I_customerForm, $hydrator);
    }
}

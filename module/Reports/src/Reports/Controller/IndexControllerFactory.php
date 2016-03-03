<?php

namespace Reports\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Application\Service\UserLanguageService;

class IndexControllerFactory implements FactoryInterface
{
    /**
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedServiceLocator = $serviceLocator->getServiceLocator();

        $userLanguageService = $sharedServiceLocator->get('UserLanguageService');

        return new IndexController($userLanguageService);
    }
}

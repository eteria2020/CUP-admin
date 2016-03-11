<?php

namespace Application\Listener;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LanguageFromSessionDetectorListenerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return LanguageFromSessionDetectorListener
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedLocator = $serviceLocator->getServiceLocator();
        $params = $sharedLocator->get('Config');

        return new LanguageFromSessionDetectorListener($params);
    }
}

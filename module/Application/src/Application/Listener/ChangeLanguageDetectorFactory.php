<?php

namespace Application\Listener;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ChangeLanguageDetectorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $languageService = $serviceLocator->get('LanguageService');
        $params = $serviceLocator->get('Config')['languageSession'];

        return new ChangeLanguageDetector($languageService, $params);
    }
}

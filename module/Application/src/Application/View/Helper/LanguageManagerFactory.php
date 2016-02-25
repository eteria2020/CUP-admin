<?php
namespace Application\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LanguageManagerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {

        $sm = $serviceLocator->getServiceLocator();
        
        $config = $sm->get('config');
        $languages = $config['translation_config']['languages'];

        $userLanguageService = $sm->get('UserLanguageService');

        return new LanguageManager($languages, $userLanguageService);
    }
}

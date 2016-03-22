<?php
namespace Application\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LanguageMenuHelperFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return LanguageMenuHelper
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sm = $serviceLocator->getServiceLocator();

        $config = $sm->get('config');
        $languages = $config['translation_config']['languages'];

        $languageService = $sm->get('LanguageService');

        return new LanguageMenuHelper($languages, $languageService);
    }
}

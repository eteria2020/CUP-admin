<?php

namespace Application\Controller\Plugin;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TranslatorPluginFactory implements FactoryInterface
{
    /**
     * Default method to be used in a Factory Class
     *
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     * @param ServiceLocatorInterface $serviceLocator
     * @return TranslatorPlugin|mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {

        $languageService = $serviceLocator->getServiceLocator()->get('LanguageService');
        $translator = $languageService->getTranslator();

        return new TranslatorPlugin($translator);
    }
}

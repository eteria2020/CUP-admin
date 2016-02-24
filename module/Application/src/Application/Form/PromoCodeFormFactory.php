<?php

namespace Application\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PromoCodeFormFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return PromoCodeForm
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $promoCodeService = $serviceLocator->get('SharengoCore\Service\PromoCodesService');

        $languageService = $serviceLocator->get('LanguageService');
        $translator = $languageService->getTranslator();

        $promoCodeFieldset = new PromoCodeFieldset($promoCodeService, $translator);

        return new PromoCodeForm($promoCodeFieldset);
    }
}
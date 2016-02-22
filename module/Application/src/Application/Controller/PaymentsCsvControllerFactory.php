<?php

namespace Application\Controller;

use Application\Form\CsvUploadForm;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PaymentsCsvControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedServiceManager = $serviceLocator->getServiceLocator();
        $csvService = $sharedServiceManager->get('SharengoCore\Service\CsvService');
        $contractsService = $sharedServiceManager->get('Cartasi\Service\CartasiContracts');

        $languageService = $sharedServiceManager->get('LanguageService');
        $translator = $languageService->getTranslator();

        $form = new CsvUploadForm($translator);

        return new PaymentsCsvController(
            $csvService,
            $contractsService,
            $form
        );
    }
}

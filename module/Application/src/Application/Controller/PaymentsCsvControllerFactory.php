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
        $csvService = $sharedServiceManager->get('SharengoCore\Service\CartasiCsvAnalyzeService');
        $contractsService = $sharedServiceManager->get('Cartasi\Service\CartasiContracts');
        $transactionsService = $sharedServiceManager->get('Cartasi\Service\CartasiTransactionsService');

        $languageService = $sharedServiceManager->get('LanguageService');
        $translator = $languageService->getTranslator();

        $form = new CsvUploadForm($translator);

        return new PaymentsCsvController(
            $csvService,
            $contractsService,
            $transactionsService,
            $form
        );
    }
}

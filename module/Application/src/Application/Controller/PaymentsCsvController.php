<?php

namespace Application\Controller;

use Cartasi\Exception\InvalidCsvException;
use Cartasi\Exception\InvalidPathException;
use SharengoCore\Service\CsvService;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class PaymentsCsvController extends AbstractActionController
{
    /**
     * @var CsvService
     */
    private $csvService;

    /**
     * @param CsvService $csvService
     */
    public function __construct(
        CsvService $csvService
    ) {
        $this->csvService = $csvService;
    }

    public function csvAction()
    {
        $newFiles = $this->csvService->searchForNewFiles();
        $csvFiles = $this->csvService->getAllFiles();
        $csvResolvedAnomalies = $this->csvService->getAllResolvedAnomalies();
        $csvUnresolvedAnomalies = $this->csvService->getAllUnresolvedAnomalies();

        return new ViewModel([
            'newFiles' => $newFiles,
            'csvFiles' => $csvFiles,
            'csvResolvedAnomalies' => $csvResolvedAnomalies,
            'csvUnresolvedAnomalies' => $csvUnresolvedAnomalies
        ]);
    }

    public function addFileAction()
    {
        $filename = $this->params()->fromQuery('filename');
        $this->csvService->addFile($filename);

        return $this->reload();
    }

    public function analyzeFileAction()
    {
        $csvFileId = $this->params()->fromQuery('csvFileId');
        $csvFile = $this->csvService->getFileById($csvFileId);
        $this->csvService->analyzeFile($csvFile);

        return $this->reload();
    }

    private function reload()
    {
        return $this->redirect()->toRoute('payments/csv');
    }
}

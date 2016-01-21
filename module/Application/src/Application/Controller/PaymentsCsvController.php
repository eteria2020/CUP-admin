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
        $csvFile = $this->csvService->addFile($filename, $this->identity());
        $this->csvService->analyzeFile($csvFile);

        return $this->reload();
    }

    public function detailsAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $csvAnomaly = $this->csvService->getAnomalyById($id);

        return new ViewModel([
            'csvAnomaly' => $csvAnomaly
        ]);
    }

    public function addNoteAction()
    {
        $id = $this->params()->fromRoute('id', 0);
        $csvAnomaly = $this->csvService->getAnomalyById($id);

        if ($this->getRequest()->isPost()) {
            try {
                $postData = $this->getRequest()->getPost()->toArray();
                $content = $postData['new-note'];
                $this->csvService->addNoteToAnomaly($csvAnomaly, $this->identity(), $content);
                $this->flashMessenger()->addSuccessMessage('Nota aggiunta con successo');
            } catch (NoteContentNotValidException $e) {
                $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
                $this->flashMessenger()->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
                $this->flashMessenger()->addErrorMessage('Errore nell\'inserimento della nota');
            }
        }

        return $this->redirect()->toRoute('payments/csv-details', ['id' => $csvAnomaly->getId()]);
    }

    private function reload()
    {
        return $this->redirect()->toRoute('payments/csv');
    }
}

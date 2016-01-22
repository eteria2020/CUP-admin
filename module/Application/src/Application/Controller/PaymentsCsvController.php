<?php

namespace Application\Controller;

use Cartasi\Exception\InvalidCsvException;
use Cartasi\Exception\InvalidPathException;
use Cartasi\Service\CartasiContractsService;
use SharengoCore\Exception\CartasiCsvAnomalyAlreadyResolvedException;
use SharengoCore\Service\CsvService;

use Zend\Http\Response;
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
     * @var CartasiContractsService
     */
    private $contractsService;

    /**
     * @param CsvService $csvService
     * @param CartasiContractsService $contractsService
     */
    public function __construct(
        CsvService $csvService,
        CartasiContractsService $contractsService
    ) {
        $this->csvService = $csvService;
        $this->contractsService = $contractsService;
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

        return $this->redirect()->toRoute('payments/csv');
    }

    public function detailsAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $csvAnomaly = $this->csvService->getAnomalyById($id);

        // Fetch customer
        $customer = null;
        try {
            $contractId = $csvAnomaly->getCsvData()['num_contratto'];
            $contract = $this->contractsService->getContractById($contractId);
            $customer = $contract->getCustomer();
        } catch (ContractNotFoundException $e) {

        }

        return new ViewModel([
            'csvAnomaly' => $csvAnomaly,
            'customer' => $customer
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

        return $this->reloadDetails($csvAnomaly->getId());
    }

    public function resolveAction()
    {
        $id = $this->params()->fromRoute('id', 0);
        $csvAnomaly = $this->csvService->getAnomalyById($id);

        try {
            $this->csvService->resolveAnomaly($csvAnomaly, $this->identity());
            $this->flashMessenger()->addSuccessMessage('Anomalia risolta con successo');
        } catch (CartasiCsvAnomalyAlreadyResolvedException $e) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
            $this->flashMessenger()->addErrorMessage('Anomalia già segnata come risolta');
        } catch (\Exception $e) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
            $this->flashMessenger()->addErrorMessage('Errore nella risoluzione');
        }

        return $this->reloadDetails($csvAnomaly->getId());
    }

    private function reloadDetails($id)
    {
        return $this->redirect()->toRoute('payments/csv-details', ['id' => $id]);
    }
}

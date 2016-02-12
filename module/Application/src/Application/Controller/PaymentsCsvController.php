<?php

namespace Application\Controller;

use Application\Form\CsvUploadForm;
use Cartasi\Exception\InvalidCsvException;
use Cartasi\Exception\InvalidPathException;
use Cartasi\Exception\ContractNotFoundException;
use Cartasi\Service\CartasiContractsService;
use SharengoCore\Exception\CartasiCsvAnomalyAlreadyResolvedException;
use SharengoCore\Service\CsvService;
use SharengoCore\Entity\CartasiCsvAnomaly;

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
     * @var CsvUploadForm
     */
    private $form;

    /**
     * @param CsvService $csvService
     * @param CartasiContractsService $contractsService
     * @param CsvUploadForm $form
     */
    public function __construct(
        CsvService $csvService,
        CartasiContractsService $contractsService,
        CsvUploadForm $form
    ) {
        $this->csvService = $csvService;
        $this->contractsService = $contractsService;
        $this->form = $form;
    }

    public function csvAction()
    {
        $newFiles = $this->csvService->searchForNewFiles();
        $csvFiles = $this->csvService->getAllFiles();
        $csvResolvedAnomalies = $this->csvService->getAllResolvedAnomalies();
        $csvUnresolvedAnomalies = $this->csvService->getAllUnresolvedAnomalies();
        $url = $this->url()->fromRoute('payments/csv-upload');
        $this->form->setAttribute('action', $url);

        return new ViewModel([
            'form' => $this->form,
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

        return $this->reloadList();
    }

    public function detailsAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $csvAnomaly = $this->csvService->getAnomalyById($id);

        if (!$csvAnomaly instanceof CartasiCsvAnomaly) {
            $this->response->setStatusCode(Response::STATUS_CODE_404);
            return;
        }

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

    public function uploadAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            // Make certain to merge the files info!
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            $this->form->setData($post);
            if ($this->form->isValid()) {
                $data = $this->form->getData();
                $csvFile = $this->csvService->addFile(
                    $data['csv-upload']['tmp_name'],
                    $this->identity(),
                    true,
                    $data['csv-upload']['name']
                );

                try {
                    $this->csvService->analyzeFile($csvFile);
                    $this->flashMessenger()->addSuccessMessage('File caricato con successo');
                } catch (InvalidCsvException $e) {
                    $this->flashMessenger()->addErrorMessage('Formattazione del file non valida');
                }
            } else {
                foreach ($this->form->getMessages() as $message) {
                    $this->flashMessenger()->addErrorMessage($message);
                }
            }
        }

        return $this->reloadList();
    }

    private function reloadList()
    {
        return $this->redirect()->toRoute('payments/csv');
    }

    private function reloadDetails($id)
    {
        return $this->redirect()->toRoute('payments/csv-details', ['id' => $id]);
    }
}

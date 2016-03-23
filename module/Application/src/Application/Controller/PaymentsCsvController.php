<?php

namespace Application\Controller;

use Application\Form\CsvUploadForm;
use Cartasi\Exception\InvalidCsvException;
use Cartasi\Exception\InvalidPathException;
use Cartasi\Exception\ContractNotFoundException;
use Cartasi\Service\CartasiContractsService;
use SharengoCore\Exception\CartasiCsvAnomalyAlreadyResolvedException;
use SharengoCore\Service\CartasiCsvAnalyzeService;
use SharengoCore\Entity\CartasiCsvAnomaly;
use Cartasi\Service\CartasiTransactionsService;

use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class PaymentsCsvController extends AbstractActionController
{
    /**
     * @var CartasiCsvAnalyzeService
     */
    private $cartasiCsvAnalyzeService;

    /**
     * @var CartasiContractsService
     */
    private $contractsService;

    /**
     * @var CartasiTransactionsService
     */
    private $transactionsService;

    /**
     * @var CsvUploadForm
     */
    private $form;

    /**
     * @param CartasiCsvAnalyzeService $cartasiCsvAnalyzeService
     * @param CartasiContractsService $contractsService
     * @param CsvUploadForm $form
     */
    public function __construct(
        CartasiCsvAnalyzeService $cartasiCsvAnalyzeService,
        CartasiContractsService $contractsService,
        CartasiTransactionsService $transactionsService,
        CsvUploadForm $form
    ) {
        $this->cartasiCsvAnalyzeService = $cartasiCsvAnalyzeService;
        $this->contractsService = $contractsService;
        $this->transactionsService = $transactionsService;
        $this->form = $form;
    }

    public function csvAction()
    {
        $newFiles = $this->cartasiCsvAnalyzeService->searchForNewFiles();
        $csvFiles = $this->cartasiCsvAnalyzeService->getAllFiles();
        $csvResolvedAnomalies = $this->cartasiCsvAnalyzeService->getAllResolvedAnomalies();
        $csvUnresolvedAnomalies = $this->cartasiCsvAnalyzeService->getAllUnresolvedAnomalies();
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
        $csvFile = $this->cartasiCsvAnalyzeService->addFile($filename, $this->identity());
        $this->cartasiCsvAnalyzeService->analyzeFile($csvFile);

        return $this->reloadList();
    }

    public function detailsAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $csvAnomaly = $this->cartasiCsvAnalyzeService->getAnomalyById($id);
        $transaction = $csvAnomaly->getTransaction();
        $transactionType = $this->transactionsService->getPaymentTypeFromTransaction($transaction);

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
            'customer' => $customer,
            'transactionType' => $transactionType
        ]);
    }

    public function addNoteAction()
    {
        $translator = $this->TranslatorPlugin();
        $id = $this->params()->fromRoute('id', 0);
        $csvAnomaly = $this->cartasiCsvAnalyzeService->getAnomalyById($id);

        if ($this->getRequest()->isPost()) {
            try {
                $postData = $this->getRequest()->getPost()->toArray();
                $content = $postData['new-note'];
                $this->cartasiCsvAnalyzeService->createAnomalyNote($csvAnomaly, $this->identity(), $content);
                $this->flashMessenger()->addSuccessMessage($translator->translate('Nota aggiunta con successo'));
            } catch (NoteContentNotValidException $e) {
                $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
                $this->flashMessenger()->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
                $this->flashMessenger()->addErrorMessage($translator->translate('Errore nell\'inserimento della nota'));
            }
        }

        return $this->reloadDetails($csvAnomaly->getId());
    }

    public function resolveAction()
    {
        $translator = $this->TranslatorPlugin();
        $id = $this->params()->fromRoute('id', 0);
        $csvAnomaly = $this->cartasiCsvAnalyzeService->getAnomalyById($id);

        try {
            $this->cartasiCsvAnalyzeService->resolveAnomaly($csvAnomaly, $this->identity());
            $this->flashMessenger()->addSuccessMessage($translator->translate('Anomalia risolta con successo'));
        } catch (CartasiCsvAnomalyAlreadyResolvedException $e) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
            $this->flashMessenger()->addErrorMessage($translator->translate('Anomalia già segnata come risolta'));
        } catch (\Exception $e) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
            $this->flashMessenger()->addErrorMessage($translator->translate('Errore nella risoluzione'));
        }

        return $this->reloadDetails($csvAnomaly->getId());
    }

    public function uploadAction()
    {
        $translator = $this->TranslatorPlugin();
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
                $csvFile = $this->cartasiCsvAnalyzeService->addFile(
                    $data['csv-upload']['tmp_name'],
                    $this->identity(),
                    true,
                    $data['csv-upload']['name']
                );

                try {
                    $this->cartasiCsvAnalyzeService->analyzeFile($csvFile);
                    $this->flashMessenger()->addSuccessMessage($translator->translate('File caricato con successo'));
                } catch (InvalidCsvException $e) {
                    $this->flashMessenger()->addErrorMessage($translator->translate('Formattazione del file non valida'));
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

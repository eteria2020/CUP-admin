<?php

namespace Application\Controller;

use SharengoCore\Service\ForeignDriversLicenseService;
use SharengoCore\Service\ValidateForeignDriversLicenseService;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Http\Response\Stream;
use Zend\Http\Headers;

class ForeignDriversLicenseController extends AbstractActionController
{
    /**
     * @var ForeignDriversLicenseService
     */
    private $foreignDriversLicenseService;

    /**
     * @var ValidateForeignDriversLicenseService
     */
    private $validateForeignDriversLicenseService;

    public function __construct(
        ForeignDriversLicenseService $foreignDriversLicenseService,
        ValidateForeignDriversLicenseService $validateForeignDriversLicenseService
    ) {
        $this->foreignDriversLicenseService = $foreignDriversLicenseService;
        $this->validateForeignDriversLicenseService = $validateForeignDriversLicenseService;
    }

    public function uploadedFilesAction()
    {
        return new ViewModel();
    }

    public function datatableAction()
    {
        $filters = $this->params()->fromPost();
        $filters['withLimit'] = true;
        $dataDataTable = $this->foreignDriversLicenseService->getDataDataTable($filters);
        $total = $this->foreignDriversLicenseService->getTotalUploadedFiles();
        $recordsFiltered = $this->getRecordsFiltered($filters, $total);

        return new JsonModel([
            'draw'            => $this->params()->fromQuery('sEcho', 0),
            'recordsTotal'    => $total,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $dataDataTable
        ]);
    }

    private function getRecordsFiltered($filters, $total)
    {
        if (empty($filters['searchValue'])) {
            return $total;
        } else {
            $filters['withLimit'] = false;

            return $this->foreignDriversLicenseService->getDataDataTable($filters, true);
        }
    }

    public function downloadAction()
    {
        $fileUploadId = $this->params()->fromRoute('id');

        $foreignDriversLicenseUpload = $this->foreignDriversLicenseService->getUploadedFileById($fileUploadId);

        $file = $foreignDriversLicenseUpload->fileLocation();

        $response = new Stream();
        $response->setStream(fopen($file, 'r'));
        $response->setStatusCode(200);
        $response->setStreamName(basename($file));

        $headers = new Headers();
        $headers->addHeaders([
            'Content-Disposition' => 'attachment; filename="' .basename($file) . '"',
            'Content-Type' => 'application/octet-stream',
            'Content-Length' => filesize($file)
        ]);
        $response->setHeaders($headers);

        return $response;
    }

    public function validateAction()
    {
        $translator = $this->TranslatorPlugin();
        $fileUploadId = $this->params()->fromRoute('id');

        $foreignDriversLicenseUpload = $this->foreignDriversLicenseService->getUploadedFileById($fileUploadId);

        try {
            $this->validateForeignDriversLicenseService->validateForeignDriversLicense(
                $foreignDriversLicenseUpload,
                $this->identity()
            );

            $this->flashMessenger()->addSuccessMessage($translator->translate('Patente validata con successo.'));
        } catch (\Exception $e) {
            $this->flashMessenger()->addErrorMessage($translator->translate('Si è verificato un errore nella validazione della patente'));
        }

        $this->redirect()->toRoute('customers/foreign-drivers-license');
    }

    public function revokeAction()
    {
        $translator = $this->TranslatorPlugin();
        $fileUploadId = $this->params()->fromRoute('id');

        $foreignDriversLicenseUpload = $this->foreignDriversLicenseService->getUploadedFileById($fileUploadId);

        try {
            $this->validateForeignDriversLicenseService->revokeForeignDriversLicense(
                $foreignDriversLicenseUpload,
                $this->identity()
            );

            $this->flashMessenger()->addSuccessMessage($translator->translate('Patente revocata con successo.'));
        } catch (\Exception $e) {
            $this->flashMessenger()->addErrorMessage($translator->translate('Si è verificato un errore durante la revoca della patente'));
        }

        $this->redirect()->toRoute('customers/foreign-drivers-license');
    }
}

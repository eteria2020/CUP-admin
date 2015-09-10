<?php

namespace Application\Controller;

use Application\Form\ExtraPaymentsForm;
use SharengoCore\Service\TripPaymentsService;
use SharengoCore\Service\PaymentsService;
use SharengoCore\Service\CustomersService;
use SharengoCore\Service\ExtraPaymentsService;
use Cartasi\Service\CartasiContractsService;
use Cartasi\Service\CartasiCustomerPayments;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class PaymentsController extends AbstractActionController
{
    /**
     * @var TripPaymentsService
     */
    private $tripPaymentsService;

    /**
     * @var PaymentsService
     */
    private $paymentsService;

    /**
     * @var CustomersService
     */
    private $customersService;

    /**
     * @var ExtraPaymentsForm
     */
    private $extraPaymentsForm;

    /**
     * @var CartasiContractsService
     */
    private $cartasiContractsService;

    /**
     * @var CartasiCustomerPayments
     */
    private $cartasiCustomerPayments;

    /**
     * @var ExtraPaymentsService
     */
    private $extraPaymentsService;

    public function __construct(
        TripPaymentsService $tripPaymentsService,
        PaymentsService $paymentsService,
        CustomersService $customersService,
        ExtraPaymentsForm $extraPaymentsForm,
        CartasiContractsService $cartasiContractsService,
        CartasiCustomerPayments $cartasiCustomerPayments,
        ExtraPaymentsService $extraPaymentsService
    ) {
        $this->tripPaymentsService = $tripPaymentsService;
        $this->paymentsService = $paymentsService;
        $this->customersService = $customersService;
        $this->extraPaymentsForm = $extraPaymentsForm;
        $this->cartasiContractsService = $cartasiContractsService;
        $this->cartasiCustomerPayments = $cartasiCustomerPayments;
        $this->extraPaymentsService = $extraPaymentsService;
    }

    public function failedPaymentsAction()
    {
        return new ViewModel();
    }

    public function failedPaymentsDatatableAction()
    {
        $filters = $this->params()->fromPost();
        $filters['withLimit'] = true;
        $dataDataTable = $this->tripPaymentsService->getFailedPaymentsData($filters);
        $totalFailedPayments = $this->tripPaymentsService->getTotalFailedPayments();
        $recordsFiltered = $this->getRecordsFiltered($filters, $totalFailedPayments);

        return new JsonModel([
            'draw'            => $this->params()->fromQuery('sEcho', 0),
            'recordsTotal'    => $totalFailedPayments,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $dataDataTable
        ]);
    }

    protected function getRecordsFiltered($filters, $totalTripPayments)
    {
        if (empty($filters['searchValue']) && !isset($filters['columnValueWithoutLike'])) {

            return $totalTripPayments;

        } else {

            $filters['withLimit'] = false;

            return count($this->tripPaymentsService->getFailedPaymentsData($filters));
        }
    }

    public function retryAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);

        $tripPayment = $this->tripPaymentsService->getTripPaymentById($id);

        if (!$tripPayment->isWrongPayment()) {
            return $this->notFoundAction();
        }

        $tripPaymentTries = $tripPayment->getTripPaymentTries();

        return new ViewModel([
            'tripPayment' => $tripPayment,
            'tripPaymentTries' => $tripPaymentTries,
            'customer' => $tripPayment->getCustomer()
        ]);
    }

    public function doRetryAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);

        $tripPayment = $this->tripPaymentsService->getTripPaymentById($id);

        // the second parameter is needed to avoid sending an email to the customer
        $cartasiResponse = $this->paymentsService->tryTripPayment($tripPayment, true);

        if ($cartasiResponse->getOutcome() === 'OK') {
            $this->customersService->setCustomerPaymentAble($tripPayment->getCustomer());
        }

        return new JsonModel([
            'outcome' => $cartasiResponse->getOutcome(),
            'message' => $cartasiResponse->getMessage()
        ]);
    }

    public function extraAction()
    {
        return new ViewModel([
            'form' => $this->extraPaymentsForm
        ]);
    }

    public function payExtraAction()
    {
        $customerId = $this->params()->fromPost('customerId');
        $paymentType = $this->params()->fromPost('paymentType');
        $reason = $this->params()->fromPost('reason');
        $amount = $this->params()->fromPost('amount');

        try {
            $customer = $this->customersService->findById($customerId);

            if (is_null($customer)) {
                $this->getResponse()->setStatusCode(422);
                return new JsonModel([
                    'error' => 'Non esiste un cliente per l\'id specificato'
                ]);
            }

            $contract = $this->cartasiContractsService->getCartasiContract($customer);

            if (is_null($contract)) {
                $this->getResponse()->setStatusCode(422);
                return new JsonModel([
                    'error' => 'Il cliente non ha un contratto valido con Cartasi'
                ]);
            }

            $response = $this->cartasiCustomerPayments->sendPaymentRequest($customer, $amount);

            if (!$response->getCompletedCorrectly()) {
                $this->response->setStatusCode(402);
                return new JsonModel([
                    'error' => 'Il tentativo di pagamento non è andato a buon fine. Il cliente è stato notificato da Cartasi'
                ]);
            }

            $extraPayment = $this->extraPaymentsService->registerExtraPayment(
                $customer,
                $amount,
                $paymentType,
                $reason
            );

            return new JsonModel([
                'message' => 'Il tentativo di pagamento è andato a buon fine. Il cliente è stato notificato da Cartasi'
            ]);
        } catch (\Exception $e) {
            $this->response->setStatusCode(500);
            return new JsonModel([
                'error' => 'C\'è stato un errore durante la procedura di pagamento: ' . $e->getMessage()
            ]);
        }
    }
}

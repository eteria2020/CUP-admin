<?php

namespace Application\Controller;

use SharengoCore\Service\TripPaymentsService;
use SharengoCore\Service\PaymentsService;
use SharengoCore\Service\CustomersService;

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

    public function __construct(
        TripPaymentsService $tripPaymentsService,
        PaymentsService $paymentsService,
        CustomersService $customersService
    ) {
        $this->tripPaymentsService = $tripPaymentsService;
        $this->paymentsService = $paymentsService;
        $this->customersService = $customersService;
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

        return new JsonModel([
            'draw'            => $this->params()->fromQuery('sEcho', 0),
            'recordsTotal'    => $totalFailedPayments,
            'recordsFiltered' => count($dataDataTable),
            'data'            => $dataDataTable
        ]);
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
}

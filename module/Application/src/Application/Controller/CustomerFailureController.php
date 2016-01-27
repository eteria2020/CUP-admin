<?php
namespace Application\Controller;

use SharengoCore\Service\CustomersService;
use SharengoCore\Service\TripPaymentsService;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class CustomerFailureController extends AbstractActionController
{
    /**
     * @var CustomersService
     */
    private $customersService;

    /**
     * @var TripPaymentsService
     */
    private $tripPaymentsService;

    /**
     * @param CustomersService $customersService
     * @param TripPaymentsService $tripPaymentsService
     */
    public function __construct(
        CustomersService $customersService,
        TripPaymentsService $tripPaymentsService
    ) {
        $this->customersService = $customersService;
        $this->tripPaymentsService = $tripPaymentsService;
    }

    public function failureTabAction()
    {
        $id = $this->params()->fromRoute('id', 0);
        $customer = $this->customersService->findById($id);

        $tripPayments = $this->tripPaymentsService->getFailedByCustomer($customer);

        $view = new ViewModel([
            'tripPayments' => $tripPayments
        ]);
        $view->setTerminal(true);

        return $view;
    }
}

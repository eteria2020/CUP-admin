<?php
namespace Application\Controller;

use SharengoCore\Service\CustomersService;
use SharengoCore\Service\TripPaymentsService;
use SharengoCore\Service\ExtraPaymentsService;

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
     * @var ExtraPaymentsService
     */
    private $extraPaymentsService;

    /**
     * @param CustomersService $customersService
     * @param TripPaymentsService $tripPaymentsService
     * @param ExtraPaymentsService $extraPaymentsService
     */
    public function __construct(
        CustomersService $customersService,
        TripPaymentsService $tripPaymentsService,
        ExtraPaymentsService $extraPaymentsService
    ) {
        $this->customersService = $customersService;
        $this->tripPaymentsService = $tripPaymentsService;
        $this->extraPaymentsService = $extraPaymentsService;
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
    
    public function extraTabAction()
    {
        $id = $this->params()->fromRoute('id', 0);
        $customer = $this->customersService->findById($id);

        $extraPayments = $this->extraPaymentsService->getFailedByCustomer($customer);

        $view = new ViewModel([
            'extraPayments' => $extraPayments
        ]);
        $view->setTerminal(true);

        return $view;
    }
}

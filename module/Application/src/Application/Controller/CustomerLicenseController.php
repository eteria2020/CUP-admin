<?php
namespace Application\Controller;

use SharengoCore\Entity\CustomerDeactivation;
use SharengoCore\Service\CustomersService;
use SharengoCore\Service\CustomerDeactivationService;
use SharengoCore\Service\DriversLicenseValidationService;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class CustomerLicenseController extends AbstractActionController
{
    /**
     * @var CustomersService
     */
    private $customersService;

    /**
     * @var DriversLicenseValidationService
     */
    private $validationService;

    /**
     * @var CustomerDeactivationService
     */
    private $deactivationService;

    /**
     * @param CustomersService $customersService
     * @param DriversLicenseValidationService $validationService
     * @param CustomerDeactivationService $deactivationService
     */
    public function __construct(
        CustomersService $customersService,
        DriversLicenseValidationService $validationService,
        CustomerDeactivationService $deactivationService
    ) {
        $this->customersService = $customersService;
        $this->validationService = $validationService;
        $this->deactivationService = $deactivationService;
    }

    public function licenseTabAction()
    {
        $id = $this->params()->fromRoute('id', 0);
        $customer = $this->customersService->findById($id);

        $validations = $this->validationService->getByCustomer($customer);
        $isValidated = !$this->deactivationService->hasActiveDeactivations(
            $customer,
            CustomerDeactivation::INVALID_DRIVERS_LICENSE
        );

        $view = new ViewModel([
            'validations' => $validations,
            'isValidated' => $isValidated,
            'isLicenseForeign' => $customer->getDriverLicenseForeign()
        ]);
        $view->setTerminal(true);

        return $view;
    }
}

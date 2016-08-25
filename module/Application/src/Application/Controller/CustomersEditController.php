<?php
namespace Application\Controller;

use Application\Form\CustomerForm;
use Application\Form\DriverForm;
use Application\Form\SettingForm;
use SharengoCore\Entity\Customers;
use SharengoCore\Exception\CustomerNotFoundException;
use SharengoCore\Service\CustomerDeactivationService;
use SharengoCore\Service\CustomersService;
use SharengoCore\Service\TripPaymentTriesService;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Form;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class CustomersEditController extends AbstractActionController
{
    /**
     * @var CustomersService
     */
    private $customersService;

    /**
     * @var CustomerDeactivationService
     */
    private $deactivationService;

    /**
     * @var DoctrineHydrator
     */
    private $hydrator;

    /**
     * @var CustomerForm
     */
    private $customerForm;

    /**
     * @var DriverForm
     */
    private $driverForm;

    /**
     * @var SettingForm
     */
    private $settingForm;

    /**
     * @param CustomersService $customersService
     * @param CustomerDeactivationService $deactivationService
     * @param TripPaymentTriesService $tripPaymentTriesService
     * @param DoctrineHydrator $hydrator
     * @param CustomerForm $customerForm
     * @param DriverForm $driverForm
     * @param SettingForm $settingForm
     */
    public function __construct(
        CustomersService $customersService,
        CustomerDeactivationService $deactivationService,
        TripPaymentTriesService $tripPaymentTriesService,
        DoctrineHydrator $hydrator,
        CustomerForm $customerForm,
        DriverForm $driverForm,
        SettingForm $settingForm
    ) {
        $this->customersService = $customersService;
        $this->deactivationService = $deactivationService;
        $this->tripPaymentTriesService = $tripPaymentTriesService;
        $this->hydrator = $hydrator;
        $this->customerForm = $customerForm;
        $this->driverForm = $driverForm;
        $this->settingForm = $settingForm;
    }

    public function editTabAction()
    {
        $customer = $this->getCustomer();

        $form = $this->customerForm;
        $formDriver = $this->driverForm;
        $formSetting = $this->settingForm;
        $customerData = $this->hydrator->extract($customer);
        $form->setData(['customer' => $customerData]);
        $formDriver->setData(['driver' => $customerData]);
        $formSetting->setData(['setting' => $customerData]);
        $deactivations = $this->deactivationService->getAllActive($customer);
        $webuserRole = $this->identity()->getRole();

        $view = new ViewModel([
            'customer' => $customer,
            'customerForm' => $form,
            'driverForm' => $formDriver,
            'settingForm' => $formSetting,
            'deactivations' => $deactivations,
        ]);

        $view->setTerminal(true);
        return $view;
    }

    /**
     * @return Customers
     * @throws CustomerNotFoundException
     */
    protected function getCustomer()
    {
        $id = $this->params()->fromRoute('id', 0);
        $customer = $this->customersService->findById($id);

        if (is_null($customer)) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);
            throw new CustomerNotFoundException();
        }

        return $customer;
    }

    /**
     * Creates a CustomerDeactivation for the current Customer
     */
    public function deactivateAction()
    {
        $translator = $this->TranslatorPlugin();
        $customer = $this->getCustomer();
        $webuser = $this->identity();

        try {
            $this->deactivationService->deactivateByWebuser($customer, $webuser);
            $this->flashMessenger()->addSuccessMessage($translator->translate('Utente disattivato'));
        } catch (Exception $e) {
            $this->flashMessenger()->addErrorMessage($translator->translate('Operazione fallita'));
        }

        return $this->reloadTab($customer);
    }

    /**
     * Removes all CustomerDeactivations for the current Customer.
     * If a tripPaymentTries id is specified, it reactivates only FAILED_PAYMENT
     * deactivations
     */
    public function reactivateAction()
    {
        $translator = $this->TranslatorPlugin();
        $customer = $this->getCustomer();
        $webuser = $this->identity();

        try {
            /*
             * If there is a TripPaymentTries id then reactivate only for that.
             * This part has been added to expose this functionality without
             * duplicating code
             */
            if ($this->params()->fromPost('tripPaymentTriesId') !== null) {
                $tripPaymentTry = $this->tripPaymentTriesService->getById(
                    $this->params()->fromPost('tripPaymentTriesId')
                );
                $this->deactivationService->reactivateCustomerForTripPaymentTry(
                    $customer,
                    $tripPaymentTry,
                    $webuser
                );
                $this->flashMessenger()->addSuccessMessage(
                    $translator->translate('Riattivazione per pagamento riuscito completata')
                );
                return new JsonModel();
            /*
             * If no params are provided, reactivate all.
             * This is the part used by the customers-edit view
             */
            } else {
                $this->deactivationService->reactivateCustomer($customer, $webuser);
                $this->flashMessenger()->addSuccessMessage($translator->translate('Utente riattivato'));
            }
        } catch (Exception $e) {
            $this->flashMessenger()->addErrorMessage($translator->translate('Operazione fallita'));
        }

        return $this->reloadTab($customer);
    }

    /**
     * Removes the specified CustomerDeactivation for the current Customer
     */
    public function editDeactivationAction()
    {
        $translator = $this->TranslatorPlugin();
        $customer = $this->getCustomer();
        $webuser = $this->identity();
        $deactivationId = $this->params()->fromQuery('deactivationId', 0);

        try {
            $deactivation = $this->deactivationService->getById($deactivationId);
            $this->deactivationService->reactivateByWebuser($deactivation, $webuser);
            $this->flashMessenger()->addSuccessMessage($translator->translate('Disattivazione rimossa'));
        } catch (Exception $e) {
            $this->flashMessenger()->addErrorMessage($translator->translate('Operazione fallita'));
        }

        return $this->reloadTab($customer);
    }

    /**
     * Reloads the page keeping the selected tab
     *
     * @param Customers $customer
     */
    private function reloadTab(Customers $customer)
    {
        return $this->redirect()->toRoute(
            'customers/edit',
            ['id' => $customer->getId()],
            ['query' => ['tab' => 'edit']]
        );
    }
}

<?php
namespace Application\Controller;

use Application\Form\CustomerForm;
use Application\Form\DriverForm;
use Application\Form\SettingForm;
use SharengoCore\Entity\Customers;
use SharengoCore\Exception\CustomerNotFoundException;
use SharengoCore\Service\CustomerDeactivationService;
use SharengoCore\Service\CustomersService;

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
     * @param DoctrineHydrator $hydrator
     * @param CustomerForm $customerForm
     * @param DriverForm $driverForm
     * @param SettingForm $settingForm
     */
    public function __construct(
        CustomersService $customersService,
        CustomerDeactivationService $deactivationService,
        DoctrineHydrator $hydrator,
        CustomerForm $customerForm,
        DriverForm $driverForm,
        SettingForm $settingForm
    ) {
        $this->customersService = $customersService;
        $this->deactivationService = $deactivationService;
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
        $deactivations = $this->deactivationService->getAll($customer);

        $view = new ViewModel([
            'customer' => $customer,
            'customerForm' => $form,
            'driverForm' => $formDriver,
            'settingForm' => $formSetting,
            'deactivations' => $deactivations
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
        $customer = $this->getCustomer();
        $webuser = $this->identity();

        try {
            $this->deactivationService->deactivateByWebuser($customer, $webuser);
            $this->flashMessenger()->addSuccessMessage('Utente disattivato');
        } catch (Exception $e) {
            $this->flashMessenger()->addErrorMessage('Operazione fallita');
        }

        return $this->reloadTab($customer);
    }

    /**
     * Removes all CustomerDeactivations for the current Customer
     */
    public function reactivateAction()
    {
        $customer = $this->getCustomer();
        $webuser = $this->identity();

        try {
            $this->deactivationService->reactivateCustomer($customer, $webuser);
            $this->flashMessenger()->addSuccessMessage('Utente riattivato');
        } catch (Exception $e) {
            $this->flashMessenger()->addErrorMessage('Operazione fallita');
        }

        return $this->reloadTab($customer);
    }

    /**
     * Removes the specified CustomerDeactivation for the current Customer
     */
    public function editDeactivationAction()
    {
        $customer = $this->getCustomer();
        $webuser = $this->identity();
        $deactivationId = $this->params()->fromQuery('deactivationId', 0);

        try {
            $deactivation = $this->deactivationService->getById($deactivationId);
            $this->deactivationService->reactivateByWebuser($deactivation, $webuser);
            $this->flashMessenger()->addSuccessMessage('Disattivazione rimossa');
        } catch (Exception $e) {
            $this->flashMessenger()->addErrorMessage('Operazione fallita');
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

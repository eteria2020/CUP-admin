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
        $deactivations = $this->deactivationService
            ->getCustomerDeactivations($customer);

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

    public function deactivateAction()
    {
        $customer = $this->getCustomer();

        $this->flashMessenger()->addSuccessMessage('deactivate action');

        return $this->reloadTab();
    }

    public function editDeactivationAction()
    {
        $customer = $this->getCustomer();

        $this->flashMessenger()->addSuccessMessage('edit deactivation action');

        return $this->reloadTab();
    }

    /**
     * Reloads the page keeping the selected tab
     */
    private function reloadTab()
    {
        return $this->redirect()->toRoute(
            'customers/edit',
            ['id' => $customer->getId()],
            ['query' => ['tab' => 'edit']]
        );
    }
}

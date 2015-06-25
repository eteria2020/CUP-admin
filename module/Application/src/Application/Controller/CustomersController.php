<?php
namespace Application\Controller;

use Application\Form\CustomerForm;
use Application\Form\DriverForm;
use SharengoCore\Entity\Customers;
use SharengoCore\Service\CardsService;
use SharengoCore\Service\CustomersService;
use Zend\Form\Form;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class CustomersController extends AbstractActionController
{
    /**
     * @var CustomersService
     */
    private $I_customerService;

    /**
     * @var CardsService
     */
    private $I_cardsService;

    /**
     * @var
     */
    private $I_customerForm;

    /**
     * @var
     */
    private $I_driverForm;


    /**
     * @var
     */
    private $I_settingForm;

    /**
     * @var \Zend\Stdlib\Hydrator\HydratorInterface
     */
    private $hydrator;

    /**
     * @param CustomersService $I_customerService
     */
    public function __construct(
        CustomersService $I_customerService,
        CardsService $I_cardsService,
        Form $I_customerForm,
        Form $I_driverForm,
        Form $I_settingForm,
        HydratorInterface $hydrator
    ) {
        $this->I_customerService = $I_customerService;
        $this->I_cardsService = $I_cardsService;
        $this->I_customerForm = $I_customerForm;
        $this->I_driverForm = $I_driverForm;
        $this->I_settingForm = $I_settingForm;
        $this->hydrator = $hydrator;
    }

    public function listAction()
    {
        return new ViewModel([
            'totalCustomers' => $this->I_customerService->getTotalCustomers()
        ]);
    }

    public function editAction()
    {
        /** @var Customers $I_customer */
        $I_customer = $this->getCustomer();
        
        $form = null;

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();

            switch($postData['type']) {

                case 'customer':
                    $form = $this->I_customerForm;
                    $postData['customer']['id'] = $I_customer->getId();
                    $postData['customer']['name'] = $I_customer->getName();
                    $postData['customer']['surname'] = $I_customer->getSurname();
                    $postData['customer']['email'] = $I_customer->getEmail();
                    $postData['customer']['taxCode'] = $I_customer->getTaxCode();
                    $postData['customer']['birthDate'] = $I_customer->getBirthDate()->format('Y-m-d');

                    $this->I_customerService->setValidatorEmail($I_customer->getEmail());
                    $this->I_customerService->setValidatorTaxCode($I_customer->getTaxCode());
                    break;

                case 'driver':
                    $form = $this->I_driverForm;
                    $postData['driver']['id'] = $I_customer->getId();
                    break;

                case 'setting':
                    $form = $this->I_settingForm;
                    $postData['setting']['id'] = $I_customer->getId();
                    break;
            }

            $form->setData($postData);

            if ($form->isValid()) {

                try {

                    $this->I_customerService->saveData($form->getData());
                    $this->flashMessenger()->addSuccessMessage('Modifica effettuta con successo!');

                } catch (\Exception $e) {
                    $this->flashMessenger()->addErrorMessage($e->getMessage());
                }

                return $this->redirect()->toRoute('customers/edit', [
                   'controller' => 'Customers',
                   'action' =>  'edit',
                       'id' => $I_customer->getId()
                   ]);
            }
        }

        return new ViewModel([
            'customer' => $I_customer,
        ]);
    }

    public function datatableAction()
    {
        $as_filters = $this->params()->fromPost();
        $as_filters['withLimit'] = true;
        $as_dataDataTable = $this->I_customerService->getDataDataTable($as_filters);
        $i_userTotal = $this->I_customerService->getTotalCustomers();
        $i_recordsFiltered = $this->_getRecordsFiltered($as_filters, $i_userTotal);

        return new JsonModel([
            'draw'            => $this->params()->fromQuery('sEcho', 0),
            'recordsTotal'    => $i_userTotal,
            'recordsFiltered' => $i_recordsFiltered,
            'data'            => $as_dataDataTable
        ]);
    }

    public function infoTabAction()
    {
        /** @var Customers $I_customer */
        $I_customer = $this->getCustomer();

        $form = $this->I_customerForm;
        $formDriver = $this->I_driverForm;
        $formSetting = $this->I_settingForm;
        $customerData = $this->hydrator->extract($I_customer);
        $form->setData(['customer' => $customerData]);
        $formDriver->setData(['driver' => $customerData]);
        $formSetting->setData(['setting' => $customerData]);

        $view = new ViewModel([
            'customer'     => $I_customer,
            'customerForm' => $form,
            'driverForm'   => $formDriver,
            'settingForm'  => $formSetting
        ]);
        $view->setTerminal(true);

        return $view;
    }

    public function editTabAction()
    {
        /** @var Customers $I_customer */
        $I_customer = $this->getCustomer();

        $form = $this->I_customerForm;
        $formDriver = $this->I_driverForm;
        $formSetting = $this->I_settingForm;
        $customerData = $this->hydrator->extract($I_customer);
        $form->setData(['customer' => $customerData]);
        $formDriver->setData(['driver' => $customerData]);
        $formSetting->setData(['setting' => $customerData]);

        $view = new ViewModel([
            'customer'     => $I_customer,
            'customerForm' => $form,
            'driverForm'   => $formDriver,
            'settingForm'  => $formSetting
        ]);
        $view->setTerminal(true);

        return $view;
    }

    public function bonusTabAction()
    {
        /** @var Customers $I_customer */
        $I_customer = $this->getCustomer();

        $view = new ViewModel([
            'customer'  => $I_customer,
            'listBonus' => $this->I_customerService->getAllBonus($I_customer)

        ]);
        $view->setTerminal(true);

        return $view;
    }

    public function cardTabAction()
    {
        /** @var Customers $I_customer */
        $I_customer = $this->getCustomer();

        $view = new ViewModel([
            'customer'  => $I_customer,
        ]);
        $view->setTerminal(true);

        return $view;
    }

    public function removeCardAction()
    {
        /** @var Customers $I_customer */
        $I_customer = $this->getCustomer();
        $status = 'error';

        if ($this->getRequest()->isPost()) {

            try {

                $this->I_customerService->removeCard($I_customer);
                $status = 'success';

            } catch (\Exception $e) {

                $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
            }
        }

        return new JsonModel([
            'status' => $status
        ]);
    }

    public function assignCardAction()
    {
        /** @var Customers $I_customer */
        $I_customer = $this->getCustomer();
        $status = 'error';

        if ($this->getRequest()->isPost()) {

            try {

                $postData = $this->getRequest()->getPost()->toArray();
                $I_card = $this->I_cardsService->getCard($postData['rfid']);

                $this->I_customerService->assignCard($I_customer, $I_card, true);
                $status = 'success';

            } catch (\Exception $e) {

                $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
            }
        }

        return new JsonModel([
            'status' => $status
        ]);
    }

    public function ajaxCardCodeAction()
    {
        $query = $this->params()->fromQuery('query', '');
        return new JsonModel($this->I_cardsService->autoCompleteAjax($query));
    }

    protected function _getRecordsFiltered($as_filters, $i_totalCustomer)
    {
        if (empty($as_filters['searchValue'])) {

            return $i_totalCustomer;

        } else {

            $as_filters['withLimit'] = false;

            return count($this->I_customerService->getDataDataTable($as_filters));
        }
    }

    protected function getCustomer()
    {
        $id = $this->params()->fromRoute('id', 0);

        /** @var Customers $I_customer */
        $I_customer = $this->I_customerService->findById($id);

        if (is_null($I_customer)) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);

            return false;
        }

        return $I_customer;
    }
}

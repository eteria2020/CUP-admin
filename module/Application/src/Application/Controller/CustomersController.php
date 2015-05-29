<?php
namespace Application\Controller;

use Application\Form\CustomerForm;
use SharengoCore\Entity\Customers;
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
     * @var
     */
    private $I_customerForm;

    /**
     * @var \Zend\Stdlib\Hydrator\HydratorInterface
     */
    private $hydrator;

    /**
     * @param CustomersService $I_customerService
     */
    public function __construct(CustomersService $I_customerService, Form $I_customerForm, HydratorInterface $hydrator)
    {
        $this->I_customerService = $I_customerService;
        $this->I_customerForm = $I_customerForm;
        $this->hydrator = $hydrator;
    }

    public function listAction()
    {
        return new ViewModel();
    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id', 0);

        /** @var Customers $I_customer */
        $I_customer = $this->I_customerService->findById($id);

        if (is_null($I_customer)) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);

            return false;
        }

        /** @var CustomerForm $form */
        $form = $this->I_customerForm;
        $customerData = $this->hydrator->extract($I_customer);
        $form->setData(['customer' => $customerData]);

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();
            $postData['customer']['id'] = $id;
            $form->setData($postData);

            $this->I_customerService->setValidatorEmail($I_customer->getEmail());
            $this->I_customerService->setValidatorTaxCode($I_customer->getTaxCode());

            if ($form->isValid()) {

                try {

                    $form->saveData();
                    $this->flashMessenger()->addSuccessMessage('Modifica effettuta con successo!');

                } catch (\Exception $e) {
                    $this->flashMessenger()->addErrorMessage($e->getMessage());

                }

                return $this->redirect()->toRoute('customers');
            }
        }

        return new ViewModel(array(
            'customer'     => $I_customer,
            'customerForm' => $this->I_customerForm
        ));
    }

    public function datatableAction()
    {
        $as_filters = $this->params()->fromPost();
        $as_filters['withLimit'] = true;
        $as_dataDataTable = $this->I_customerService->getDataDataTable($as_filters);
        $i_userTotal = $this->I_customerService->getTotalCustomers();
        $i_recordsFiltered = $this->_getRecordsFiltered($as_filters, $i_userTotal);

        return new JsonModel(array(
            'draw'            => $this->params()->fromQuery('sEcho', 0),
            'recordsTotal'    => $i_userTotal,
            'recordsFiltered' => $i_recordsFiltered,
            'data'            => $as_dataDataTable
        ));
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
}
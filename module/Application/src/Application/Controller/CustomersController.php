<?php
namespace Application\Controller;

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
        $I_customer = $this->I_customerService->findById($id);

        if (is_null($I_customer)) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);
            return false;
        }

        $form = $this->I_customerForm;
        $customerData = $this->hydrator->extract($I_customer);
        $form->setData($customerData);

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();
            $form->setData($postData);

            if ($form->isValid()) {

                $form->saveData();

                $this->flashMessenger()->addSuccessMessage('ok');

                return $this->redirect()->toRoute('customers');

            } else {
                print_r($form->getMessages());
                exit;
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

        return new JsonModel(array(
            'draw'            => $this->params()->fromQuery('sEcho', 0),
            'recordsTotal'    => 100,//$i_userTotal['numero'],
            'recordsFiltered' => 100,//$i_recordsFiltered,
            'data'            => $as_dataDataTable
        ));
    }
}
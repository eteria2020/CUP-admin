<?php
namespace Application\Controller;

use SharengoCore\Service\CustomerNoteService;
use SharengoCore\Service\CustomersService;

use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class CustomerNoteController extends AbstractActionController
{
    /**
     * @var CustomersService
     */
    private $customersService;

    /**
     * @var CustomerNoteService
     */
    private $customerNoteService;

    /**
     * @param CustomersService $customersService
     * @param CustomerNoteService $customerNoteService
     */
    public function __construct(
        CustomersService $customersService,
        CustomerNoteService $customerNoteService
    ) {
        $this->customersService = $customersService;
        $this->customerNoteService = $customerNoteService;
    }

    public function notesTabAction()
    {
        $id = $this->params()->fromRoute('id', 0);
        $customer = $this->customersService->findById($id);

        $notes = $this->customerNoteService->getByCustomer($customer);

        $view = new ViewModel([
            'customer' => $customer,
            'notes' => $notes
        ]);
        $view->setTerminal(true);

        return $view;
    }

    public function addNoteAction()
    {
        $id = $this->params()->fromRoute('id', 0);
        $customer = $this->customersService->findById($id);
        $status = 'error';

        if ($this->getRequest()->isPost()) {
            try {
                $postData = $this->getRequest()->getPost()->toArray();
                $this->customerNoteService->addNote($customer, $this->identity(), $postData['new-note']);
                $this->flashMessenger()->addSuccessMessage('OK');
                $status = 'ok';
            } catch (\Exception $e) {
                $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
                $this->flashMessenger()->addErrorMessage('KO');
            }
        }

        return $this->redirect()->toRoute('customers/edit', ['id' => $customer->getId()], ['query' => ['tab' => 'notes']]);
    }
}

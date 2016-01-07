<?php
namespace Application\Controller;

use Application\Form\CustomerForm;
use Application\Form\DriverForm;
use SharengoCore\Entity\Customers;
use SharengoCore\Entity\PromoCodes;
use SharengoCore\Service\CardsService;
use SharengoCore\Service\CustomersService;
use SharengoCore\Service\PromoCodesService;
use SharengoCore\Service\DisableContractService;
use SharengoCore\Exception\CustomerNotFoundException;
use SharengoCore\Exception\BonusAssignmentException;
use Cartasi\Service\CartasiContractsService;

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
    private $customersService;

    /**
     * @var CardsService
     */
    private $cardsService;

    /**
     * @var
     */
    private $customerForm;

    /**
     * @var
     */
    private $driverForm;


    /**
     * @var
     */
    private $settingForm;

    /**
     * @var
     */
    private $promoCodeForm;

    /**
     * @var
     */
    private $customerBonusForm;

    /**
     * @var
     */
    private $cardForm;

    /**
     * @var \Zend\Stdlib\Hydrator\HydratorInterface
     */
    private $hydrator;

    /** @var  PromoCodesService */
    private $promoCodeService;

    /**
     * @var CartasiContractsService
     */
    private $cartsiContractsService;

    /**
     * @var DisableContractService
     */
    private $disableContractService;

    /**
     * @param CustomersService $customersService
     * @param CardsService $cardsService
     * @param PromoCodesService $promoCodeService
     * @param Form $customerForm
     * @param Form $driverForm
     * @param Form $settingForm
     * @param Form $promoCodeForm
     * @param Form customerBonusForm
     * @param HydratorInterface $hydrator
     * @param CartasiContractsService $cartasiContractsService
     * @param DisableContractService $disableContractService
     */
    public function __construct(
        CustomersService $customersService,
        CardsService $cardsService,
        PromoCodesService $promoCodeService,
        Form $customerForm,
        Form $driverForm,
        Form $settingForm,
        Form $promoCodeForm,
        Form $customerBonusForm,
        Form $cardForm,
        HydratorInterface $hydrator,
        CartasiContractsService $cartasiContractsService,
        DisableContractService $disableContractService
    ) {
        $this->customersService = $customersService;
        $this->cardsService = $cardsService;
        $this->promoCodeService =  $promoCodeService;
        $this->customerForm = $customerForm;
        $this->driverForm = $driverForm;
        $this->settingForm = $settingForm;
        $this->promoCodeForm = $promoCodeForm;
        $this->customerBonusForm = $customerBonusForm;
        $this->cardForm = $cardForm;
        $this->hydrator = $hydrator;
        $this->cartasiContractsService = $cartasiContractsService;
        $this->disableContractService = $disableContractService;
    }

    public function listAction()
    {
        return new ViewModel([
            'totalCustomers' => $this->customersService->getTotalCustomers()
        ]);
    }

    public function editAction()
    {
        /** @var Customers $customer */
        $customer = $this->getCustomer();
        $tab = $this->params()->fromQuery('tab', 'info');

        $form = null;

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();

            switch ($postData['type']) {
                case 'customer':
                    $form = $this->customerForm;
                    $postData['customer']['id'] = $customer->getId();
                    $postData['customer']['name'] = $customer->getName();
                    $postData['customer']['surname'] = $customer->getSurname();
                    $postData['customer']['email'] = $customer->getEmail();
                    $postData['customer']['taxCode'] = $customer->getTaxCode();
                    $postData['customer']['birthDate'] = $customer->getBirthDate()->format('Y-m-d');

                    // ensure vat is not NULL but a string
                    if (is_null($postData['customer']['vat'])) {
                        $postData['customer']['birthDate'] = '';
                    }

                    $this->customersService->setValidatorEmail($customer->getEmail());
                    $this->customersService->setValidatorTaxCode($customer->getTaxCode());
                    break;

                case 'driver':
                    $form = $this->driverForm;
                    $postData['driver']['id'] = $customer->getId();
                    break;

                case 'setting':
                    $form = $this->settingForm;
                    $postData['setting']['id'] = $customer->getId();
                    $postData['setting']['enabled'] = $customer->getEnabled() ? 'true' : 'false';
                    $postData['setting']['goldList'] =
                        $postData['setting']['goldList'] |
                        $postData['setting']['maintainer'];
                    break;
            }

            $form->setData($postData);

            if ($form->isValid()) {
                try {
                    $this->customersService->saveData($form->getData());
                    $this->flashMessenger()->addSuccessMessage('Modifica effettuta con successo!');

                } catch (\Exception $e) {
                    $this->flashMessenger()->addErrorMessage('Si è verificato un errore applicativo. L\'assistenza tecnica è già al corrente, ci scusiamo per l\'inconveniente');
                }

                return $this->redirect()->toRoute('customers/edit', ['id' => $customer->getId()]);
            } else {
                $this->flashMessenger()->addErrorMessage('Dati inseriti non validi');
            }
        }

        return new ViewModel([
            'customer' => $customer,
            'tab'      => $tab
        ]);
    }

    public function datatableAction()
    {
        $as_filters = $this->params()->fromPost();
        $as_filters['withLimit'] = true;
        $as_dataDataTable = $this->customersService->getDataDataTable($as_filters);
        $userTotal = $this->customersService->getTotalCustomers();
        $recordsFiltered = $this->_getRecordsFiltered($as_filters, $userTotal);

        return new JsonModel([
            'draw'            => $this->params()->fromQuery('sEcho', 0),
            'recordsTotal'    => $userTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $as_dataDataTable
        ]);
    }

    public function infoTabAction()
    {
        /** @var Customers $customer */
        $customer = $this->getCustomer();

        $form = $this->customerForm;
        $formDriver = $this->driverForm;
        $formSetting = $this->settingForm;
        $customerData = $this->hydrator->extract($customer);
        $form->setData(['customer' => $customerData]);
        $formDriver->setData(['driver' => $customerData]);
        $formSetting->setData(['setting' => $customerData]);

        $view = new ViewModel([
            'customer'     => $customer,
            'customerForm' => $form,
            'driverForm'   => $formDriver,
            'settingForm'  => $formSetting
        ]);
        $view->setTerminal(true);

        return $view;
    }

    public function bonusTabAction()
    {
        /** @var Customers $customer */
        $customer = $this->getCustomer();

        $view = new ViewModel([
            'customer'      => $customer,
            'listBonus'     => $this->customersService->getAllBonus($customer),
        ]);
        $view->setTerminal(true);

        return $view;
    }

    public function cardTabAction()
    {
        /** @var Customers $customer */
        $customer = $this->getCustomer();

        $view = new ViewModel([
            'customer'  => $customer,
        ]);
        $view->setTerminal(true);

        return $view;
    }

    public function removeCardAction()
    {
        /** @var Customers $customer */
        $customer = $this->getCustomer();
        $status = 'error';

        if ($this->getRequest()->isPost()) {
            try {
                $this->customersService->removeCard($customer);
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
        /** @var Customers $customer */
        $customer = $this->getCustomer();
        $status = 'error';

        if ($this->getRequest()->isPost()) {
            try {
                $postData = $this->getRequest()->getPost()->toArray();
                $card = $this->cardsService->getCard($postData['code']);

                $this->customersService->assignCard($customer, $card, true);
                $status = 'success';
            } catch (\Exception $e) {
                $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
            }
        }

        return new JsonModel([
            'status' => $status
        ]);
    }

    public function ajaxCardCodeAutocompleteAction()
    {
        $query = $this->params()->fromQuery('query', '');
        return new JsonModel($this->cardsService->ajaxCardCodeAutocomplete($query));
    }

    public function contractTabAction()
    {
        $customer = $this->getCustomer();
        $contract = $this->cartasiContractsService->getCartasiContract($customer);

        $view = new ViewModel([
            'customer' => $customer,
            'contract' => $contract
        ]);
        $view->setTerminal(true);

        return $view;
    }

    public function disableContractAction()
    {
        $contractId = $this->params()->fromPost('contractId');

        $contract = $this->cartasiContractsService->getContractById($contractId);

        try {
            $this->disableContractService->disableContract($contract);

            $this->flashMessenger()->addSuccessMessage('Contratto disabilitato correttamente!');
        } catch (\Exception $e) {
            $this->flashMessenger()->addErrorMessage('Errore durante la disabilitazione del contratto');
        }

        return new JsonModel();
    }

    public function assignPromoCodeAction()
    {
        $customer = $this->getCustomer();
        $form = $this->promoCodeForm;

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();
            $form->setData($postData);

            if ($form->isValid()) {
                try {
                    /** @var PromoCodes $promoCode */
                    $promoCode = $this->promoCodeService->getPromoCode($postData['promocode']['promocode']);

                    $this->customersService->addBonusFromPromoCode($customer, $promoCode);

                    $this->flashMessenger()->addSuccessMessage('Operazione completata con successo!');
                } catch (BonusAssignmentException $e) {
                    $this->flashMessenger()->addErrorMessage($e->getMessage());
                } catch (\Exception $e) {
                    $this->flashMessenger()->addErrorMessage('Si è verificato un errore applicativo. L\'assistenza tecnica è già al corrente, ci scusiamo per l\'inconveniente');

                    return $this->redirect()->toRoute('customers/assign-promo-code', ['id' => $customer->getId()]);
                }

                return $this->redirect()->toRoute('customers/edit', ['id' => $customer->getId()], ['query' => ['tab' => 'bonus']]);
            }
        }

        return new ViewModel([
            'customer' => $customer,
            'promoCodeForm' => $form
        ]);
    }

    public function addBonusAction()
    {
        /** @var Customers $customer */
        $customer = $this->getCustomer();
        $form = $this->customerBonusForm;

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();
            $form->setData($postData);

            if ($form->isValid()) {
                try {
                    $this->customersService->addBonusFromWebUser($customer, $form->getData());

                    $this->flashMessenger()->addSuccessMessage('Operazione completata con successo!');
                } catch (\Exception $e) {
                    $this->flashMessenger()->addErrorMessage('Si è verificato un errore applicativo. L\'assistenza tecnica è già al corrente, ci scusiamo per l\'inconveniente');

                    return $this->redirect()->toRoute('customers/add-bonus', ['id' => $customer->getId()]);
                }

                return $this->redirect()->toRoute('customers/edit', ['id' => $customer->getId()], ['query' => ['tab' => 'bonus']]);
            }
        }

        return new ViewModel([
            'customer' => $customer,
            'promoCodeForm' => $form
        ]);
    }

    public function removeBonusAction()
    {
        $status = 'error';

        if ($this->getRequest()->isPost()) {
            try {
                $postData = $this->getRequest()->getPost()->toArray();
                $bonus = $this->customersService->findBonus($postData['bonus']);

                if ($this->customersService->removeBonus($bonus)) {
                    $status = 'success';
                }
            } catch (\Exception $e) {
                $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
            }
        }

        return new JsonModel([
            'status' => $status
        ]);
    }

    public function listCardAction()
    {
        return new ViewModel([]);
    }

    public function listCardsDatatableAction()
    {
        $filters = $this->params()->fromPost();
        $filters['withLimit'] = true;
        $dataDataTable = $this->cardsService->getDataDataTable($filters);
        $cardsTotal = $this->cardsService->getTotalCards();
        $recordsFiltered = $this->_getRecordsFilteredCards($filters, $cardsTotal);

        return new JsonModel([
            'draw' => $this->params()->fromQuery('sEcho', 0),
            'recordsTotal' => $cardsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $dataDataTable
        ]);
    }

    public function addCardAction()
    {
        $customer = null;
        $customerId = $this->params()->fromQuery('customer', 0);

        if ($customerId) {
            $customer = $this->customerService->findById($customerId);

            if (is_null($customer)) {
                $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);

                return false;
            }
        }

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();
            $form->setData($postData);

            if ($this->cardForm->isValid()) {
                try {
                    $this->cardsService->createCard($this->cardForm->getData(), $customer);
                    $this->flashMessenger()->addSuccessMessage('Operazione completata con successo!');
                } catch (\Exception $e) {
                    $this->flashMessenger()->addErrorMessage('Qualcosa è andata storto durante la creazione');
                }

                if (!is_null($customer)) {
                    return $this->redirect()->toRoute('customers/edit', ['id' => $customer->getId()], ['query' => ['tab' => 'card']]);
                }

                return $this->redirect()->toRoute('customers/list-card');
            }
        }

        return new ViewModel([
            'customer' => $customer,
            'cardForm' => $this->cardForm
        ]);
    }

    protected function _getRecordsFiltered($filters, $totalCustomer)
    {
        if (empty($filters['searchValue'])) {
            return $totalCustomer;
        } else {
            $filters['withLimit'] = false;

            return count($this->customersService->getDataDataTable($filters));
        }
    }

    protected function _getRecordsFilteredCards($as_filters, $i_totalCards)
    {
        if (empty($as_filters['searchValue'])) {
            return $i_totalCards;
        } else {
            $as_filters['withLimit'] = false;

            return count($this->I_cardsService->getDataDataTable($as_filters));
        }
    }

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

    public function invoicesTabAction()
    {
        $customer = $this->getCustomer();

        $view = new ViewModel([
            'customer'  => $customer,
        ]);

        $view->setTerminal(true);

        return $view;
    }

    public function infoAction()
    {
        try {
            $customer = $this->getCustomer();

            if (!$customer) {
                throw new \Exception();
            }

            return new JsonModel([
                'id' => $customer->getId(),
                'name' => $customer->getName(),
                'surname' => $customer->getSurname()
            ]);
        } catch (\Exception $e) {
            $this->getResponse()->setStatusCode(422);
            return new JsonModel([
                'error' => 'Non esiste un cliente per l\'id specificato'
            ]);
        }
    }
}

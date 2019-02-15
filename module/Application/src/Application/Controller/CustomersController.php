<?php
namespace Application\Controller;

// Internals
use SharengoCore\Entity\Customers;
use SharengoCore\Entity\CustomersBonus;
use SharengoCore\Entity\CustomersPoints;
use SharengoCore\Entity\PromoCodes;
use SharengoCore\Service\BonusService;
use SharengoCore\Service\PointService;
use SharengoCore\Service\CardsService;
use SharengoCore\Service\CustomersBonusPackagesService;
use SharengoCore\Service\CustomersService;
use SharengoCore\Service\CustomerDeactivationService;
use SharengoCore\Service\PromoCodesService;
use SharengoCore\Service\DisableContractService;
use Application\Service\RegistrationService;
use SharengoCore\Exception\CustomerNotFoundException;
use SharengoCore\Exception\BonusAssignmentException;
use Cartasi\Service\CartasiContractsService;
use SharengoCore\Service\EmailService;
use SharengoCore\Entity\UserEvents;
use SharengoCore\Service\UserEventsService;
use SharengoCore\Service\OldCustomerDiscountsService;
// Externals
use Zend\Form\Form;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

class CustomersController extends AbstractActionController
{
    /**
     * @var CustomersService
     */
    private $customersService;
    
    /**
     * @var CustomerDeactivationService
     */
    private $customerDeactivationService;

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
    private $customerPointForm;

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
    private $cartasiContractsService;

    /**
     * @var DisableContractService
     */
    private $disableContractService;

    /**
     * @var BonusService
     */
    private $bonusService;
    
    /**
     * @var PointService
     */
    private $pointService;


    /**
     * @var Container
     */
    private $datatableFiltersSessionContainer;
    
    /**
     * @var RegistrationService
     */
    private $registrationService;
    
    /**
     * @var EmailService
     */
    private $emailService;
    
    /**
     * @var UserEventsService
     */
    private $userEventsService;
    
    /**
     * @var array
     */
    private $globalConfig;
    
    /**
     * @var OldCustomerDiscountsService
     */
    private $oldCustomerDiscountsService;

    /**
     * @param CustomersService $customersService
     * @param CustomerDeactivationService $customerDeactivationService
     * @param CardsService $cardsService
     * @param PromoCodesService $promoCodeService
     * @param PointService $pointService
     * @param BonusService $bonusService
     * @param Form $customerForm
     * @param Form $driverForm
     * @param Form $settingForm
     * @param Form $promoCodeForm
     * @param Form $customerPointForm
     * @param Form $customerBonusForm
     * @param Form $cardForm
     * @param HydratorInterface $hydrator
     * @param CartasiContractsService $cartasiContractsService
     * @param DisableContractService $disableContractService
     * @param Container $datatableFiltersSessionContainer
     * @param RegistrationService $registrationService
     * @param EmailService $emailService
     * @param UserEventsService $userEventsService
     * @param array $globalConfig
     * #param OldCustomerDiscountsService $oldCustomerDiscountsService
     */
    public function __construct(
        CustomersService $customersService,
        CustomerDeactivationService $customerDeactivationService,
        CardsService $cardsService,
        PromoCodesService $promoCodeService,
        BonusService $bonusService,
        PointService $pointService,
        Form $customerForm,
        Form $driverForm,
        Form $settingForm,
        Form $promoCodeForm,
        Form $customerBonusForm,
        Form $customerPointForm,
        Form $cardForm,
        HydratorInterface $hydrator,
        CartasiContractsService $cartasiContractsService,
        DisableContractService $disableContractService,
        Container $datatableFiltersSessionContainer
        ,RegistrationService $registrationService,
        EmailService $emailService,
        UserEventsService $userEventsService,
        array $globalConfig,
        OldCustomerDiscountsService $oldCustomerDiscountsService
    ) {
        $this->customersService = $customersService;
        $this->customerDeactivationService = $customerDeactivationService;
        $this->cardsService = $cardsService;
        $this->promoCodeService = $promoCodeService;
        $this->customerForm = $customerForm;
        $this->driverForm = $driverForm;
        $this->settingForm = $settingForm;
        $this->promoCodeForm = $promoCodeForm;
        $this->customerBonusForm = $customerBonusForm;
        $this->customerPointForm = $customerPointForm;
        $this->cardForm = $cardForm;
        $this->hydrator = $hydrator;
        $this->cartasiContractsService = $cartasiContractsService;
        $this->disableContractService = $disableContractService;
        $this->bonusService = $bonusService;
        $this->pointService = $pointService;
        $this->datatableFiltersSessionContainer = $datatableFiltersSessionContainer;
        $this->registrationService = $registrationService;
        $this->emailService = $emailService;
        $this->userEventsService = $userEventsService;
        $this->globalConfig = $globalConfig;
        $this->oldCustomerDiscountsService = $oldCustomerDiscountsService;
    }

    /**
     * This method return an array containing the DataTable filters,
     * from a Session Container.
     *
     * @return array
     */
    private function getDataTableSessionFilters()
    {
        return $this->datatableFiltersSessionContainer->offsetGet('Customers');
    }

    public function listAction()
    {
        $sessionDatatableFilters = $this->getDataTableSessionFilters();

        return new ViewModel([
            'totalCustomers' => $this->customersService->getTotalCustomers(),
            'filters' => json_encode($sessionDatatableFilters),
        ]);
    }

    public function editAction()
    {
        $translator = $this->TranslatorPlugin();
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
                    $postData['customer']['taxCode'] = $customer->getTaxCode();
                    $postData['customer']['birthDate'] = $customer->getBirthDate()->format('Y-m-d');

                    // Check if Webuser can edit email
                    if (!$this->isAllowed('customer', 'changeEmail')) {
                        $postData['customer']['email'] = $customer->getEmail();
                    }

                    if ($postData['customer']['vat']=='') {
                        $postData['customer']['vat'] = null;
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

                    if (!isset($postData['setting']['goldList'])) {
                        $postData['setting']['goldList'] = (int)$customer->getGoldList();
                    }
                    if (!isset($postData['setting']['maintainer'])) {
                        $postData['setting']['maintainer'] = (int)$customer->getMaintainer();
                    }                    
                    if (!isset($postData['setting']['firstPaymentCompleted'])) {
                        $postData['setting']['firstPaymentCompleted'] = (int)$customer->getFirstPaymentCompleted();
                    }
                    $postData['setting']['goldList'] =
                        $postData['setting']['goldList'] |
                        $postData['setting']['maintainer'];
                    break;
            }

            $form->setData($postData);

            if ($form->isValid()) {
                try {
                    $this->customersService->saveData($form->getData());
                    if(isset($postData['setting']) && $postData['setting']['firstPaymentCompleted'] == '1'){
                        $customer_id = $postData['setting']['id'];
                        $c = $this->customersService->findById($customer_id);
                        $this->customerDeactivationService->reactivateCustomerForFirstPaymentFromAdmin($c, $this->identity());
                    }
                    $this->flashMessenger()->addSuccessMessage($translator->translate('Modifica effettuta con successo!'));
                } catch (\Exception $e) {
                    $this->flashMessenger()->addErrorMessage($translator->translate('Si è verificato un errore applicativo. L\'assistenza tecnica è già al corrente, ci scusiamo per l\'inconveniente'));
                }

                return $this->redirect()->toRoute('customers/edit', ['id' => $customer->getId()]);
            } else {
                //add type of error
                $e = '';
                foreach ($form->getMessages() as $field) {
                    foreach ($field as $message) {
                        foreach ($message as $error) {
                            $e = $e . $error . ' ';
                        }
                    }
                }
                $this->flashMessenger()->addErrorMessage($translator->translate('Dati inseriti non validi - ' . $e));
            }
        }

        return new ViewModel([
            'customer' => $customer,
            'tab' => $tab
        ]);
    }

    public function datatableAction()
    {
        $filters = $this->params()->fromPost();
        $filters['withLimit'] = true;
        $dataDataTable = $this->customersService->getDataDataTable($filters);
        $userTotal = $this->customersService->getTotalCustomers();
        $recordsFiltered = $this->_getRecordsFiltered($filters, $userTotal);

        return new JsonModel([
            'draw' => $this->params()->fromQuery('sEcho', 0),
            'recordsTotal' => $userTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $dataDataTable
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
            'customer' => $customer,
            'customerForm' => $form,
            'driverForm' => $formDriver,
            'settingForm' => $formSetting
        ]);
        $view->setTerminal(true);

        return $view;
    }

    public function bonusTabAction()
    {
        /** @var Customers $customer */
        $customer = $this->getCustomer();

        $view = new ViewModel([
            'customer' => $customer,
            'listBonus' => $this->customersService->getAllBonus($customer),
        ]);
        $view->setTerminal(true);

        return $view;
    }

    public function pointsTabAction()
    {
        /** @var Customers $customer */
        $customer = $this->getCustomer();

        $view = new ViewModel([
            'customer' => $customer,
            'listPoints' => $this->customersService->getAllPoints($customer),
        ]);
        $view->setTerminal(true);

        return $view;
    }

    public function cardTabAction()
    {
        /** @var Customers $customer */
        $customer = $this->getCustomer();

        $view = new ViewModel([
            'customer' => $customer,
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
        $translator = $this->TranslatorPlugin();
        $contractId = $this->params()->fromPost('contractId');

        $contract = $this->cartasiContractsService->getContractById($contractId);

        try {
            $this->disableContractService->disableContract($contract);

            $this->flashMessenger()->addSuccessMessage($translator->translate('Contratto disabilitato correttamente!'));
        } catch (\Exception $e) {
            $this->flashMessenger()->addErrorMessage($translator->translate('Errore durante la disabilitazione del contratto'));
        }

        return new JsonModel();
    }

    public function assignBonusAjaxAction()
    {
        $translator = $this->TranslatorPlugin();
        $customer = $this->getCustomer();
        $postData = $this->getRequest()->getPost()->toArray();

        try {
            $bonusId = $postData['bonusId'];

            $customerBonus = $this->bonusService->getBonusFromId($bonusId);

            $this->customersService->addBonusFromWebUser($customer, $customerBonus);

            $this->flashMessenger()->addSuccessMessage($translator->translate('Operazione completata con successo!'));

        } catch (\Exception $e) {
            $this->flashMessenger()->addErrorMessage($translator->translate('Si è verificato un errore applicativo. Operazione non completata'));
        }

        return new JsonModel();
    }

    public function assignPromoCodeAction()
    {
        $translator = $this->TranslatorPlugin();
        $customer = $this->getCustomer();
        $form = $this->promoCodeForm;

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();
            $form->setData($postData);

            if ($form->isValid()) {
                try {
                    /** @var PromoCodes $promoCode */
                    $promoCode = $this->promoCodeService->getPromoCode($postData['promocode']['promocode']);

                    $this->customersService->addBonusFromPromoCodeFromWebuser($customer, $promoCode);

                    $this->flashMessenger()->addSuccessMessage($translator->translate('Operazione completata con successo!'));
                } catch (BonusAssignmentException $e) {
                    $this->flashMessenger()->addErrorMessage($e->getMessage());
                } catch (\Exception $e) {
                    $this->flashMessenger()->addErrorMessage($translator->translate('Si è verificato un errore applicativo. L\'assistenza tecnica è già al corrente, ci scusiamo per l\'inconveniente'));

                    return $this->redirect()->toRoute('customers/assign-promo-code', ['id' => $customer->getId()]);
                }

                return $this->redirect()->toRoute(
                    'customers/edit',
                    ['id' => $customer->getId()],
                    ['query' => ['tab' => 'bonus']]
                );
            }
        }

        return new ViewModel([
            'customer' => $customer,
            'promoCodeForm' => $form
        ]);
    }

    public function addBonusAction()
    {
        $translator = $this->TranslatorPlugin();
        /** @var Customers $customer */
        $customer = $this->getCustomer();
        $form = $this->customerBonusForm;

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();
            $form->setData($postData);

            if ($form->isValid()) {
                try {
                    $this->customersService->addBonusFromWebUser($customer, $form->getData());

                    $this->flashMessenger()->addSuccessMessage($translator->translate('Operazione completata con successo!'));
                } catch (\Exception $e) {
                    $this->flashMessenger()->addErrorMessage($translator->translate('Si è verificato un errore applicativo. L\'assistenza tecnica è già al corrente, ci scusiamo per l\'inconveniente'));

                    return $this->redirect()->toRoute('customers/add-bonus', ['id' => $customer->getId()]);
                }

                return $this->redirect()->toRoute(
                    'customers/edit',
                    ['id' => $customer->getId()],
                    ['query' => ['tab' => 'bonus']]
                );
            }
        }

        return new ViewModel([
            'customer' => $customer,
            'promoCodeForm' => $form
        ]);
    }
    
     public function addPointsAction()
    {
        $translator = $this->TranslatorPlugin();
        /** @var Customers $customer */
        $customer = $this->getCustomer();
        $form = $this->customerPointForm;

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();
            $form->setData($postData);

            if ($form->isValid()) {
                try {
                    $this->customersService->addPointFromWebUser($customer, $form->getData());

                    $this->flashMessenger()->addSuccessMessage($translator->translate('Operazione completata con successo!'));
                } catch (\Exception $e) {
                    $this->flashMessenger()->addErrorMessage($translator->translate('Si è verificato un errore applicativo. L\'assistenza tecnica è già al corrente, ci scusiamo per l\'inconveniente'));

                    return $this->redirect()->toRoute('customers/add-points', ['id' => $customer->getId()]);
                }

                return $this->redirect()->toRoute(
                    'customers/edit',
                    ['id' => $customer->getId()],
                    ['query' => ['tab' => 'points']]
                );
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

    public function removePointAction()
    {
        $status = 'error';

        if ($this->getRequest()->isPost()) {
            try {
                $postData = $this->getRequest()->getPost()->toArray();
                $point = $this->customersService->findPoint($postData['point']);

                if ($this->customersService->removePoint($point)) {
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
        $translator = $this->TranslatorPlugin();
        $customer = null;
        $customerId = $this->params()->fromQuery('customer', 0);

        if ($customerId) {
            $customer = $this->customersService->findById($customerId);

            if (is_null($customer)) {
                $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);

                return false;
            }
        }

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();
            $this->cardForm->setData($postData);

            if ($this->cardForm->isValid()) {
                try {
                    $this->cardsService->createCard($this->cardForm->getData(), $customer);
                    $this->flashMessenger()->addSuccessMessage($translator->translate('Operazione completata con successo!'));
                } catch (\Exception $e) {
                    $this->flashMessenger()->addErrorMessage($translator->translate('Qualcosa è andato storto durante la creazione'));
                }

                if (!is_null($customer)) {
                    return $this->redirect()->toRoute(
                        'customers/edit',
                        ['id' => $customer->getId()],
                        ['query' => ['tab' => 'card']]
                    );
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

            return $this->customersService->getDataDataTable($filters, true);
        }
    }

    protected function _getRecordsFilteredCards($filters, $totalCards)
    {
        if (empty($filters['searchValue'])) {
            return $totalCards;
        } else {
            $filters['withLimit'] = false;

            return $this->cardsService->getDataDataTable($filters, true);
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
            'customer' => $customer,
        ]);

        $view->setTerminal(true);

        return $view;
    }

    public function infoAction()
    {
        $translator = $this->TranslatorPlugin();
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
                'error' => $translator->translate('Non esiste un cliente per l\'id specificato')
            ]);
        }
    }
    
    public function resendEmailRegistrationCompliteAction() {
        $customer_id = $this->params()->fromPost('customer_id');
        $customer = $this->customersService->findById($customer_id);
        try {
            $this->registrationService->sendEmail(
                    $customer->getEmail(),
                    $customer->getName(),
                    $customer->getSurname(),
                    hash("MD5", strtoupper($customer->getEmail()).strtoupper($customer->getPassword())),
                    $customer->getLanguage());
        } catch (\Exception $e) {
            $response_msg = "error";
            $response = $this->getResponse();
            $response->setStatusCode(200);
            $response->setContent($response_msg);
            return $response;
        }
        $response_msg = "success";
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent($response_msg);
        return $response;
    }
    
    public function customerRecessAction() {
        $e = $this->globalConfig['from'];
        $customer_id = $this->params()->fromPost('customer_id');
        $customer = $this->customersService->findById($customer_id);
        try {
            $details = json_encode([
                'event' => 'recess-customer',
                'details' => array('id' => $customer->getId(),
                    'email' => $customer->getEmail(),
                    'driverLicense' => $customer->getDriverLicense(),
                    'taxCode' => $customer->getTaxCode(),
                    'mobile' => $customer->getMobile(),
                    'enabled' => $customer->getEnabled())
            ]);
            //log customer in user_events
            $userEvent = new UserEvents($this->identity(), "user", $details);
            $userEvent = $this->userEventsService->saveUserEvents($userEvent);
            
            //update customer
            $customer = $this->customersService->recessCustomer($customer);
            //send mail to servizio clienti
            //$this->sendEmailUserRecess($this->globalConfig['from'], $customer_id, 'it', 24);
            
        } catch (\Exception $e) {
            $response_msg = "error";
            $response = $this->getResponse();
            $response->setStatusCode(200);
            $response->setContent($response_msg);
            return $response;
        }
        $response_msg = "success";
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent($response_msg);
        return $response;
    }
    
    private function sendEmailUserRecess($email, $id, $language, $category) {
        $mail = $this->emailService->getMail($category, $language);
        $content = sprintf($mail->getContent(), $id);

        $attachments = [//'bannerphono.jpg' => __DIR__.'/../../../../../public/images/bannerphono.jpg'
        ];
        $this->emailService->sendEmail(
                $email, //send to
                $mail->getSubject(), //'Share’ngo: bonus 5 minuti',//object email
                $content, $attachments
        );
    }
    
    public function discountTabAction(){
        $customer = $this->getCustomer();
        $view = new ViewModel([
            'customer' => $customer,
            'old_discount' => $this->oldCustomerDiscountsService->allOldDiscounts($customer),
        ]);
        $view->setTerminal(true);

        return $view;
    }
}

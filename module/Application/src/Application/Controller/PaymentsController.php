<?php

namespace Application\Controller;

// Internals
use Application\Form\FaresForm;
use SharengoCore\Service\FaresService;
use SharengoCore\Service\TripPaymentsService;
use SharengoCore\Service\PaymentsService;
use SharengoCore\Service\CustomersService;
use SharengoCore\Service\ExtraPaymentsService;
use SharengoCore\Service\ExtraPaymentRatesService;
use SharengoCore\Service\CustomerDeactivationService;
use SharengoCore\Service\ExtraPaymentTriesService;
use Cartasi\Service\CartasiContractsService;
use Cartasi\Service\CartasiCustomerPayments;
use SharengoCore\Service\PenaltiesService;
use SharengoCore\Exception\FleetNotFoundException;
use SharengoCore\Service\FleetService;
use SharengoCore\Service\RecapService;
// Externals
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

class PaymentsController extends AbstractActionController
{
    /**
     * @var TripPaymentsService
     */
    private $tripPaymentsService;

    /**
     * @var PaymentsService
     */
    private $paymentsService;

    /**
     * @var CustomersService
     */
    private $customersService;

    /**
     * @var CartasiContractsService
     */
    private $cartasiContractsService;

    /**
     * @var CartasiCustomerPayments
     */
    private $cartasiCustomerPayments;

    /**
     * @var ExtraPaymentsService
     */
    private $extraPaymentsService;
    
    /**
     * @var ExtraPaymentTriesService
     */
    private $extraPaymentTriesService;

    /**
     * @var PenaltiesService
     */
    private $penaltiesService;

    /**
     * @var FleetService
     */
    private $fleetService;

    /**
     * @var RecapService
     */
    private $recapService;

    /**
     * @var FaresService
     */
    private $faresService;

    /**
     * @var FaresForm;
     */
    private $faresForm;

    /**
     * @var Container
     */
    private $datatableFiltersSessionContainer;
    
    /**
     * @var CustomerDeactivationService
     */
    private $deactivationService;
    
    /**
     * @var ExtraPaymentRatesService
     */
    private $extraPaymentRatesService;

    public function __construct(
        TripPaymentsService $tripPaymentsService,
        PaymentsService $paymentsService,
        CustomersService $customersService,
        CartasiContractsService $cartasiContractsService,
        CartasiCustomerPayments $cartasiCustomerPayments,
        ExtraPaymentsService $extraPaymentsService,
        ExtraPaymentTriesService $extraPaymentTriesService,
        PenaltiesService $penaltiesService,
        FleetService $fleetService,
        RecapService $recapService,
        FaresService $faresService,
        FaresForm $faresForm,
        Container $datatableFiltersSessionContainer,
        CustomerDeactivationService $deactivationService,
        ExtraPaymentRatesService $extraPaymentRatesService
    ) {
        $this->tripPaymentsService = $tripPaymentsService;
        $this->paymentsService = $paymentsService;
        $this->customersService = $customersService;
        $this->cartasiContractsService = $cartasiContractsService;
        $this->cartasiCustomerPayments = $cartasiCustomerPayments;
        $this->extraPaymentsService = $extraPaymentsService;
        $this->extraPaymentTriesService = $extraPaymentTriesService;
        $this->penaltiesService = $penaltiesService;
        $this->fleetService = $fleetService;
        $this->recapService = $recapService;
        $this->faresService = $faresService;
        $this->faresForm = $faresForm;
        $this->datatableFiltersSessionContainer = $datatableFiltersSessionContainer;
        $this->deactivationService = $deactivationService;
        $this->extraPaymentRatesService = $extraPaymentRatesService;
    }

    /**
     * This method return an array containing the DataTable filters,
     * from a Session Container.
     *
     * @return array
     */
    private function getDataTableSessionFilters()
    {
        return $this->datatableFiltersSessionContainer->offsetGet('TripPayments');
    }

    public function failedPaymentsAction()
    {
        $sessionDatatableFilters = $this->getDataTableSessionFilters();

        return new ViewModel([
            'filters' => json_encode($sessionDatatableFilters),
        ]);
    }
    
    private function getDataTableSessionExtraFilters()
    {
        return $this->datatableFiltersSessionContainer->offsetGet('ExtraPayments');
    }
    
    public function failedExtraAction()
    {
        $sessionDatatableFilters = $this->getDataTableSessionExtraFilters();

        return new ViewModel([
            'filters' => json_encode($sessionDatatableFilters),
        ]);
    }

    public function failedPaymentsDatatableAction()
    {
        $filters = $this->params()->fromPost();
        $filters['withLimit'] = true;
        
        if($filters['column'] == "" && isset($filters['columnValueWithoutLike']) && $filters['columnValueWithoutLike'] == ""){
            $filters['columnWithoutLike'] = true;
            $filters['columnValueWithoutLike'] = null;
        }
        $dataDataTable = $this->tripPaymentsService->getFailedPaymentsData($filters);
        $totalFailedPayments = $this->tripPaymentsService->getTotalFailedPayments();
        $recordsFiltered = $this->getRecordsFiltered($filters, $totalFailedPayments, "payment");

        return new JsonModel([
            'draw'            => $this->params()->fromQuery('sEcho', 0),
            'recordsTotal'    => $totalFailedPayments,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $dataDataTable
        ]);
    }
    
    public function failedExtraDatatableAction(){
        $filters = $this->params()->fromPost();
        $filters['withLimit'] = true;
        
        if($filters['column'] == "" && isset($filters['columnValueWithoutLike']) && $filters['columnValueWithoutLike'] == ""){
            $filters['columnWithoutLike'] = true;
            $filters['columnValueWithoutLike'] = null;
        }
        $dataDataTable = $this->extraPaymentsService->getFailedExtraData($filters);
        $totalFailedExtra = $this->extraPaymentsService->getTotalExtra();
        $recordsFiltered = $this->getRecordsFiltered($filters, $totalFailedExtra, "extra");

        return new JsonModel([
            'draw'            => $this->params()->fromQuery('sEcho', 0),
            'recordsTotal'    => $totalFailedExtra,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $dataDataTable
        ]);
    }

    protected function getRecordsFiltered($filters, $recordsFiltered, $param) {
        if (empty($filters['searchValue']) && !isset($filters['columnValueWithoutLike'])) {
            return $recordsFiltered;
        } else {
            $filters['withLimit'] = false;
            if ($param === "payment")
                return count($this->tripPaymentsService->getFailedPaymentsData($filters));
            else
                return count($this->extraPaymentsService->getFailedExtraData($filters));
        }
    }

    public function retryPaymentsAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);

        $tripPayment = $this->tripPaymentsService->getTripPaymentById($id);

        if (!$tripPayment->isWrongPayment()) {
            return $this->notFoundAction();
        }

        $tripPaymentTries = $tripPayment->getTripPaymentTries();

        return new ViewModel([
            'tripPayment' => $tripPayment,
            'tripPaymentTries' => $tripPaymentTries,
            'customer' => $tripPayment->getCustomer()
        ]);
    }
    
    public function retryExtraAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);

        $extraPayment = $this->extraPaymentsService->getExtraPaymentById($id);

        $extraPaymentTries = $extraPayment->getExtraPaymentTries();
        
        $extraPaymentRates = $this->extraPaymentRatesService->findByExtraPaymentFather($extraPayment->getId());
        if(count($extraPaymentRates)>0){
            $balance = $extraPayment->getAmount()-$this->extraPaymentRatesService->ratesPaidByExtraPaymentFather($extraPayment->getId());
            return new ViewModel([
                'extraPayment' => $extraPayment,
                'extraPaymentTries' => $extraPaymentTries,
                'customer' => $extraPayment->getCustomer(),
                'extraPaymentRates' => $extraPaymentRates,
                'balance' => $balance/100,
            ]);
        }else{
            $extraPayment_father = $this->extraPaymentRatesService->getExtraPaymentFather($extraPayment->getId());
            return new ViewModel([
                'extraPayment' => $extraPayment,
                'extraPaymentTries' => $extraPaymentTries,
                'customer' => $extraPayment->getCustomer(),
                'extraPaymentRates' => $extraPaymentRates,
                'extraPayment_father' => $extraPayment_father,
            ]);
        }
    }

    public function doRetryPaymentsAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);

        $webuser = $this->identity();

        $tripPayment = $this->tripPaymentsService->getTripPaymentById($id);

        if ($tripPayment->isWrongPayment()) {
            // the second parameter is needed to avoid sending an email to the customer
            $cartasiResponse = $this->paymentsService->tryTripPayment($tripPayment, $webuser, true, false, false, true);

            if ($cartasiResponse->getOutcome() === 'OK') {
                $this->customersService->enableCustomerPayment($tripPayment->getCustomer());
            }

            return new JsonModel([
                'outcome' => $cartasiResponse->getOutcome(),
                'message' => $cartasiResponse->getMessage(),
                'tripPaymentTriesId' => $tripPayment->getTripPaymentTries()[0]->getId()
            ]);
        } else {
            return new JsonModel([
                'outcome' => 'KO',
                'message' => 'The trip is not anymore in wrong payment state'
            ]);
        }
    }
    
    public function doRetryExtraAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);

        $webuser = $this->identity();

        $extraPayment = $this->extraPaymentsService->getExtraPaymentById($id);

        if ($extraPayment->isWrongExtra()) {
            // the second parameter is needed to avoid sending an email to the customer
            //$cartasiResponse = $this->paymentsService->tryExtraPayment($extraPayment, $webuser, true, false, false, true);
            $cartasiResponse = $this->paymentsService->tryExtraPayment($extraPayment, $webuser, true, false, false, false);

            
            if ($cartasiResponse->getOutcome() === 'OK') {
                //set extra payed
                $extraPayment = $this->extraPaymentsService->setPayedCorrectly($extraPayment);
                
                $this->extraPaymentsService->checkIfEnable($extraPayment);
                
                $extraPayment = $this->extraPaymentsService->setTrasaction($extraPayment, $cartasiResponse->getTransaction());
                $this->customersService->enableCustomerPayment($extraPayment->getCustomer());
            }
            
            return new JsonModel([
                'outcome' => $cartasiResponse->getOutcome(),
                'message' => $cartasiResponse->getMessage(),
                'tripPaymentTriesId' => $extraPayment->getExtraPaymentTries()[0]->getId()
            ]);
        } else {
            return new JsonModel([
                'outcome' => 'KO',
                'message' => 'The extra is not anymore in wrong payment state'
            ]);
        }
 
    }

    public function extraAction()
    {
        $penalties = $this->penaltiesService->getAllPenalties();
        $causal = $this->penaltiesService->getAllCausal();
        $fleets = $this->fleetService->getAllFleetsNoDummy();
        $types = $this->extraPaymentsService->getAllTypes();

        return new ViewModel([
            'fleets' => $fleets,
            'types' => $types,
            'penalties' => $penalties,
            'causal' => $causal
        ]);
    }


    public function setTripAsPayedAjaxAction()
    {
        $translator = $this->TranslatorPlugin();
        $id = (int)$this->params()->fromRoute('id', 0);
        $tripPayment = $this->tripPaymentsService->getTripPaymentById($id);

        try {
            $this->tripPaymentsService->setTripPaymentPayed($tripPayment);

            $this->getResponse()->setStatusCode(200);
            return new JsonModel([
                'message' => $translator->translate('La corsa è stata segnata come pagata')
            ]);
        } catch (\Exception $e) {
            $this->getResponse()->setStatusCode(500);
            return new JsonModel([
                'error' => $translator->translate('si è verificato un errore')
            ]);
        }
    }

    public function payExtraAction() 
    {
        $translator = $this->TranslatorPlugin();
        $customerId = $this->params()->fromPost('customerId');
        $fleetId = $this->params()->fromPost('fleetId');
        $type = $this->params()->fromPost('type');
        $penalty = $this->params()->fromPost('penalty');
        $reasons = $this->params()->fromPost('reasons');
        $amounts = $this->params()->fromPost('amounts');
        $rate = $this->params()->fromPost('rate');
        $n_rates = intval($this->params()->fromPost('n_rates'));

        try {
            $customer = $this->customersService->findById($customerId);

            if (is_null($customer)) {
                $this->getResponse()->setStatusCode(422);
                return new JsonModel([
                    'error' => $translator->translate('Non esiste un cliente per l\'id specificato')
                ]);
            }

            $contract = $this->cartasiContractsService->getCartasiContract($customer);

            if (is_null($contract)) {
                $this->getResponse()->setStatusCode(422);
                return new JsonModel([
                    'error' => $translator->translate('Il cliente non ha un contratto valido con Cartasi')
                ]);
            }

            try {
                $fleet = $this->fleetService->getFleetById($fleetId);
            } catch (FleetNotFoundException $e) {
                $this->getResponse()->setStatusCode(422);
                return new JsonModel([
                    'error' => $translator->translate('La flotta selezionata non è valida')
                ]);
            }

            $amount = 0;
            foreach ($amounts as $value) {
                $amount += intval($value);
            }

            if($rate == 'true'){
                $extraPaymentRate_first = $this->payExtraWithRates($customer, $fleet, $amount, $type, $penalty, $reasons, $amounts, $n_rates);
                //visto che si è scelto di pagare a rate, allora l'importo da far passare deve essere minore
                $amount = $extraPaymentRate_first->getAmount();
            }

            $response = $this->cartasiCustomerPayments->sendPaymentRequest($customer, $amount);

            $extraPayment = $this->extraPaymentsService->registerExtraPayment(
                    $customer, $fleet, $response->getTransaction(), $amount, $type, $penalty, $reasons, $amounts, true
            );
            
            //IF PAYMENTS RATES
            if($rate == 'true'){
                $extraPaymentRate_first = $this->extraPaymentRatesService->setPaymentRate($extraPaymentRate_first, $extraPayment);
            }
            
            if (!$response->getCompletedCorrectly()) {

                $extraPaymentTry = $this->extraPaymentsService->processWrongPayment($extraPayment, $response, $this->identity());

                $extraTries = $this->encodeExtra($extraPaymentTry);

                $this->response->setStatusCode(402);
                return new JsonModel([
                    'error' => $translator->translate('Il tentativo di pagamento non è andato a buon fine. Il cliente è stato notificato da Cartasi'),
                    'extraPaymentTry' => $extraTries
                ]);
            }

            $extraPaymentTry = $this->extraPaymentsService->processPayedCorrectly($extraPayment, $response, $this->identity());
            
            $extraTries = $this->encodeExtra($extraPaymentTry);

            return new JsonModel([
                'message' => $translator->translate('Il tentativo di pagamento è andato a buon fine. Il cliente è stato notificato da Cartasi'),
                'extraPaymentTry' => $extraTries
            ]);
            
        } catch (\Exception $e) {
            $this->response->setStatusCode(500);
            return new JsonModel([
                'error' => $translator->translate('C\'è stato un errore durante la procedura di pagamento: ') . $e->getMessage()
            ]);
        }
    }
    
    public function encodeExtra($extraPaymentTry) {
        $array = array(
            "date" => $extraPaymentTry->getTs()->format('Y-m-d H:i:s'),
            "webUser" => $extraPaymentTry->getWebuserName(),
            "product" => (null != $extraPaymentTry->getTransaction()) ? $extraPaymentTry->getTransaction()->getProductType() : 'n.d.',
            "outcome" => $extraPaymentTry->getOutcome(),
            "result" => (null != $extraPaymentTry->getTransaction()) ? $extraPaymentTry->getTransaction()->getOutcome() : 'n.d.',
            "message" => (null != $extraPaymentTry->getTransaction()) ? $extraPaymentTry->getTransaction()->getMessage() : 'n.d.',
            "amount" => (null != $extraPaymentTry->getExtraPayment()->getAmount()) ? $extraPaymentTry->getExtraPayment()->getAmount()/100 : 'n.d.',
        );
        return json_encode($array);
    }

    public function recapAction()
    {
        $months = null;
        $date = date("Y-m-d H:i:s");
        $fleets = null;
        $dailyIncome = null;
        $weeklyIncome = null;
        $monthlyIncome = null;

        $authorize = $this->getServiceLocator()->get('BjyAuthorize\Provider\Identity\ProviderInterface');
        $roles = $authorize->getIdentityRoles();

        if($roles[0]==='superadmin'){
            // Get months
            $months = $this->recapService->getAvailableMonths();

            // Get the selected month or default to last available
            $date = '';
            if (is_null($this->params()->fromQuery('date'))) {
                $date = $months[0]['date'];
            } else {
                $date = $this->params()->fromQuery('date');
            }

            // Get all fleets
            $fleets = $this->fleetService->getAllFleetsNoDummy();
            // Get income for each day of the selected month
            $dailyIncome = $this->recapService->getDailyIncomeForMonth($date);
            // Get income for last 4 weeks
            $weeklyIncome = $this->recapService->getWeeklyIncome();
            // Get income for last 12 months
            $monthlyIncome = $this->recapService->getMonthlyIncome();
        }

        return new ViewModel([
            'months' => $months,
            'selectedMonth' => $date,
            'isLastMonth' => $date == $months[0]['date'],
            'fleets' => $fleets,
            'daily' => $dailyIncome,
            'weekly' => $weeklyIncome,
            'monthly' => $monthlyIncome,
            'roles' => $roles
        ]);
    }

    public function faresAction()
    {
        $translator = $this->TranslatorPlugin();
        $form = $this->faresForm;

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();
            $form->setData($postData);

            if ($form->isValid()) {
                try {
                    if ($this->faresService->saveData($form->getData())) {
                        $this->flashMessenger()->addSuccessMessage($translator->translate('Tariffa creata con successo!'));
                    } else {
                        $this->flashMessenger()->addInfoMessage($translator->translate('Tariffa invariata'));
                    }
                } catch (\Exception $e) {
                    $this->flashMessenger()->addErrorMessage($translator->translate('Si è verificato un errore applicativo. L\'assistenza tecnica è già al corrente, ci scusiamo per l\'inconveniente'));

                }

                return $this->redirect()->toRoute('payments/fares');
            }
        }

        return new ViewModel([
            'faresForm' => $form
        ]);
    }
    
    
    public function setPayableAction() {
        try {
            $id = $this->params()->fromPost('id');
            $payable = $this->params()->fromPost('payable') == "true" ? true : false;

            $extraPayment = $this->extraPaymentsService->getExtraPaymentById($id);

            $extraPayment = $this->extraPaymentsService->setExtraFree($extraPayment, !$payable, $this->identity());

            $response_msg = "success";
            $response = $this->getResponse();
            $response->setStatusCode(200);
            $response->setContent($response_msg);
            return $response;
        } catch (\Exception $e) {
            $response_msg = "error";
            $response = $this->getResponse();
            $response->setStatusCode(200);
            $response->setContent($response_msg);
            return $response;
        }
    }
    
    private function payExtraWithRates($customer, $fleet, $amount, $type, $penalty, $reasons, $amounts, $n_rates) {
        //importo totale non pagabile
        $extraPaymentFather = $this->extraPaymentsService->registerExtraPayment(
                $customer, $fleet, null, $amount, $type, $penalty, $reasons, $amounts, false
        );
        //scrittutra delle rate
        $singleRate = round($amount/$n_rates);
        $amountRate = $amount;
        for($i=0;$i<$n_rates-1;$i++){
            $amountRate = $amountRate-$singleRate;
            $debit_date = new \DateTime();
            $debit_date = $debit_date->modify('+'.$i.' month');
            if($i == 0){
                $extraPaymentRate_first = $this->extraPaymentRatesService->registerExtraPaymentRate($customer, $singleRate, $debit_date, $extraPaymentFather);
            }else{
                $extraPaymentRate = $this->extraPaymentRatesService->registerExtraPaymentRate($customer, $singleRate, $debit_date, $extraPaymentFather);
            }
        }
        $debit_date = new \DateTime();
        $debit_date = $debit_date->modify('+'.$n_rates - 1 .' month');
        $lastRate = $amount-($singleRate*($n_rates-1));
        $extraPaymentRate = $this->extraPaymentRatesService->registerExtraPaymentRate($customer, $lastRate, $debit_date, $extraPaymentFather);
        
        return $extraPaymentRate_first;        
    }
}

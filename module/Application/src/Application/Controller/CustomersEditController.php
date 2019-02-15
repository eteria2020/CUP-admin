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

use BusinessCore\Form\Validator\VatNumber;
use BusinessCore\Form\Validator\ZipCode;


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
            'webuserRole' => $webuserRole,
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
                $errorMessage = "";
                if($this->customerDataIsCorrect($customer, $errorMessage)) {
                    $this->deactivationService->reactivateCustomer($customer, $webuser);
                    $this->flashMessenger()->addSuccessMessage($translator->translate('Utente riattivato'));
                } else {
                    $this->flashMessenger()->addErrorMessage($translator->translate('ATTIVAZIONE FALLITA. Dati incompleti: ').$errorMessage);
                }
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
            $allActiveDeactivations = $this->deactivationService->getAllActive($customer);

            if(count($allActiveDeactivations)===1) { // if only one deactivation request to remove, means an activation, then check the customer data
                $errorMessage ="";
                if(!$this->customerDataIsCorrect($customer, $errorMessage)) {
                    $this->flashMessenger()->addErrorMessage($translator->translate('ATTIVAZIONE FALLITA. Dati incompleti: ').$errorMessage);
                    return $this->reloadTab($customer);
                }
            }

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
     * @return Response
     */
    private function reloadTab(Customers $customer)
    {
        return $this->redirect()->toRoute(
            'customers/edit',
            ['id' => $customer->getId()],
            ['query' => ['tab' => 'edit']]
        );
    }

    /**
     * Before enable the customer we check if all data is set.
     *
     * @param Customers $customer
     * @param string $errorMessage
     * @return bool
     */
    private function customerDataIsCorrect(Customers $customer, &$errorMessage) {
        $translator = $this->TranslatorPlugin();
        $result = true;
        $errorMessage = "";


        if($this->isNullOrEmptyString($customer->getGender())){
            $errorMessage .= $translator->translate("genere utente vuoto,");
        }

        if($this->isNullOrEmptyString($customer->getName())){
            $errorMessage .= $translator->translate("nome utente vuoto,");
        }

        if($this->isNullOrEmptyString($customer->getSurname())){
            $errorMessage .= $translator->translate("cognome utente vuoto,");
        }

        if($this->isNullOrEmptyString($customer->getMobile())){
            $errorMessage .= $translator->translate("numero cellulare utente vuoto,");
        }

        if($this->isNullOrEmptyString($customer->getBirthDate())){
            $errorMessage .= $translator->translate("data nascita utente vuoto,");
        }

        if($this->isNullOrEmptyString($customer->getBirthTown())){
            $errorMessage .= $translator->translate("città nascita utente vuoto,");
        }

        if($this->isNullOrEmptyString($customer->getBirthProvince())){
            $errorMessage .= $translator->translate("provincia nascita utente vuoto,");
        }

        if($this->isNullOrEmptyString($customer->getBirthCountry())){
            $errorMessage .= $translator->translate("nazione nascita utente vuoto,");
        }

        if($this->isNullOrEmptyString($customer->getAddress())){
            $errorMessage .= $translator->translate("indirizzo utente vuoto,");
        }

        if($this->isNullOrEmptyString($customer->getTown())){
            $errorMessage .= $translator->translate("città utente vuoto,");
        }

        $validator = new ZipCode();
        if(!$validator->isValid($customer->getZipCode())){
            $errorMessage .= implode( ",", $validator->getMessages()).",";;
        }

        if($this->isNullOrEmptyString($customer->getTaxCode())){
            $errorMessage .= $translator->translate("codice fiscale vuoto,");
        }

        $validator = new VatNumber();
        if(!$this->isNullOrEmptyString($customer->getVat()) && !$validator->isValid($customer->getVat())){
            $errorMessage .= implode( ",", $validator->getMessages()).",";
        }

        if($this->isNullOrEmptyString($customer->getDriverLicenseName())){
            $errorMessage .= $translator->translate("nome patente guida vuoto,");
        }

        if($this->isNullOrEmptyString($customer->getDriverLicenseSurname())){
            $errorMessage .= $translator->translate("cognome patente guida vuoto,");
        }

        if($this->isNullOrEmptyString($customer->getDriverLicense())){
            $errorMessage .= $translator->translate("numero patente guida vuoto,");
        }

        if($this->isNullOrEmptyString($customer->getDriverLicenseCountry())){
            $errorMessage .= $translator->translate("nazione patente guida vuoto,");
        }

        if($this->isNullOrEmptyString($customer->getDriverLicenseExpire())){
            $errorMessage .= $translator->translate("scadenza patente guida vuoto,");
        }

        if($errorMessage !=="") {
            $result = false;
        }

        return $result;
    }

    /**
     * Check if $str is null or a string with spaces only
     * @param $str
     * @return bool
     */
    private function isNullOrEmptyString($str){
        $result =false;

        if (is_null($str)) {
            $result = true;
        } else {
            if(gettype($str)==='string') {
                if(trim($str)===''){
                    $result = true;
                }
            }
        }
        return $result;
    }
}

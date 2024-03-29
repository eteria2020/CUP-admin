<?php

namespace Application\Controller;

// Internals
use Application\Controller\Plugin\TranslatorPlugin;
use Application\Form\CarForm;
use Application\Listener\LanguageFromSessionDetectorListener;
use Application\Service\UserLanguageService;
use MvLabsMultilanguage\Service\LanguageService;
use SharengoCore\Entity\Cars;
use SharengoCore\Entity\CarsMaintenance;
use SharengoCore\Entity\Commands;
use SharengoCore\Entity\Configurations;

use SharengoCore\Service\CarsService;
use SharengoCore\Service\TripsService;
use SharengoCore\Service\CarsDamagesService;
use SharengoCore\Service\CommandsService;
use SharengoCore\Service\ConfigurationsService;
use SharengoCore\Service\MaintenanceLocationsService;
use SharengoCore\Service\MaintenanceMotivationsService;

use SharengoCore\Utility\CarStatus;

// Externals
use Zend\Form\Form;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

class CarsController extends AbstractActionController {

    /**
     * @var CarsService
     */
    private $carsService;

    /**
     * @var CommandsService
     */
    private $commandsService;

    /**
     * @var Form
     */
    private $carForm;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var Container
     */
    private $datatableFiltersSessionContainer;

    /**
     * @var ConfigurationsService
     */
    private $configurationsService;
    /**
     * @var TripsService
     */
    private $tripsService;

    /**
     *
     * @var string[] 
     */
    private $roles;
    
    /**
     * @var MaintenanceLocationsService
     */
    private $maintenanceLocationsService;
    
    /**
     * @var MaintenanceMotivationsService
     */

    private $maintenanceMotivationsService;

    /**
     * CarsController constructor.
     * @param CarsService $carsService
     * @param CommandsService $commandsService
     * @param Form $carForm
     * @param HydratorInterface $hydrator
     * @param Container $datatableFiltersSessionContainer
     * @param ConfigurationsService $configurationsService
     * @param TripsService $tripsService
     * @param $roles
     * @param MaintenanceLocationsService $maintenanceLocationsService
     * @param MaintenanceMotivationsService $maintenanceMotivationsService
     */
    public function __construct(
        CarsService $carsService,
        CommandsService $commandsService,
        Form $carForm,
        HydratorInterface $hydrator,
        Container $datatableFiltersSessionContainer,
        ConfigurationsService $configurationsService,
        TripsService $tripsService,
        $roles,
        MaintenanceLocationsService $maintenanceLocationsService,
        MaintenanceMotivationsService $maintenanceMotivationsService
    ) {
        $this->carsService = $carsService;
        $this->commandsService = $commandsService;
        $this->carForm = $carForm;
        $this->hydrator = $hydrator;
        $this->datatableFiltersSessionContainer = $datatableFiltersSessionContainer;
        $this->configurationsService = $configurationsService;
        $this->tripsService = $tripsService;
        $this->roles = $roles;
        $this->maintenanceLocationsService = $maintenanceLocationsService;
        $this->maintenanceMotivationsService = $maintenanceMotivationsService;
    }

    /**
     * This method return an array containing the DataTable filters,
     * from a Session Container.
     *
     * @return array
     */
    private function getDataTableSessionFilters() {
        return $this->datatableFiltersSessionContainer->offsetGet('Cars');
    }

    public function indexAction() {
        $sessionDatatableFilters = $this->getDataTableSessionFilters();

        return new ViewModel([
            'filters' => json_encode($sessionDatatableFilters),
            'roles' => $this->roles,
        ]);
    }

    public function datatableAction() {
        $as_filters = $this->params()->fromPost();
        $as_filters['withLimit'] = true;
        $as_dataDataTable = $this->carsService->getDataDataTable($as_filters);
        $i_totalCars = $this->carsService->getTotalCars();
        $i_recordsFiltered = $this->_getRecordsFiltered($as_filters, $i_totalCars);

        return new JsonModel([
            'draw' => $this->params()->fromQuery('sEcho', 0),
            'recordsTotal' => $i_totalCars,
            'recordsFiltered' => $i_recordsFiltered,
            'data' => $as_dataDataTable
        ]);
    }

    public function addAction() {
        if($this->roles[0]!='superadmin') {
            $this->redirect()->toRoute('cars');
        }
        $translator = $this->TranslatorPlugin();
        $form = $this->carForm;
        $form->setStatus([CarStatus::OPERATIVE => CarStatus::OPERATIVE]);
        $form->setFleets($this->carsService->getFleets());

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();
            $form->setData($postData);
            $form->getInputFilter()->get('location')->setRequired(false);
            $form->getInputFilter()->get('motivation')->setRequired(false);

            if ($form->isValid()) {

                try {

                    $this->carsService->saveData($form->getData());
                    $this->flashMessenger()->addSuccessMessage($translator->translate('Auto aggiunta con successo!'));
                } catch (\Exception $e) {

                    $this->flashMessenger()->addErrorMessage($translator->translate('Si è verificato un errore applicativo. L\'assistenza tecnica è già al corrente, ci scusiamo per l\'inconveniente'));
                }

                return $this->redirect()->toRoute('cars');
            }
        }

        return new ViewModel([
            'carForm' => $form
        ]);
    }

    public function editAction() {
        $plate = $this->params()->fromRoute('plate', 0);
        $car = $this->carsService->getCarByPlate($plate);
        $tab = $this->params()->fromQuery('tab', 'edit');

        return new ViewModel([
            'car' => $car,
            'tab' => $tab
        ]);
    }

    public function editTabAction() {
        $translator = $this->TranslatorPlugin();
        $plate = $this->params()->fromRoute('plate', 0);
        $car = $this->carsService->getCarByPlate($plate);
        $disableInputStatusMaintenance = false;

        if (is_null($car)) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);
            return false;
        }

        /** @var CarsMaintenance $lastUpdateCar */
        $lastCarsMaintenance = $this->carsService->getLastCarsMaintenance($car->getPlate());

        /** @var CarForm $form */
        $form = $this->carForm;
        $form->setStatus($this->carsService->getStatusCarAvailable($car->getStatus()));
        $form->setFleets($this->carsService->getFleets());
        $carData = $this->hydrator->extract($car);
        $data = [];
        $data['car'] = $carData;

        if (!is_null($lastCarsMaintenance) && $car->getStatus() == CarStatus::MAINTENANCE) {
            if(!is_null($lastCarsMaintenance->getLocationId())){
                $data['location'] = $lastCarsMaintenance->getLocationId()->getId();
            }else{
                $data['location'] = 35;//viene mostrato "Nessuna sede"
            }
            $data['note'] = $lastCarsMaintenance->getNotes();
            $data['motivation'] = $lastCarsMaintenance->getMotivation()->getId();
            $disableInputStatusMaintenance = true;
        }

        $form->setData($data);
        $lastStatus = $car->getStatus();

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();
            $postData['car']['plate'] = $car->getPlate();

            if(!isset($postData['car']['fleet'])) {
                $postData['car']['fleet'] = $car->getFleet()->getId();
            }

            $form->setData($postData);
            //$form->get('car')->remove('fleet'); // setValue($car->getFleet()->getId());
            $form->getInputFilter()->get('location')->setRequired(false);
            $form->getInputFilter()->get('motivation')->setRequired(false);

            if ($form->isValid()) {
                try {
                    $this->carsService->updateCar($form->getData(), $lastStatus, $postData, $this->identity());
                    $this->carsService->saveData($form->getData(), false);
                    $this->flashMessenger()->addSuccessMessage($translator->translate('Auto modificata con successo!'));
                } catch (\Exception $e) {

                    $this->flashMessenger()->addErrorMessage($translator->translate('Si è verificato un errore applicativo. L\'assistenza tecnica è già al corrente, ci scusiamo per l\'inconveniente'));
                }

                $url = $this->url()->fromRoute('cars/edit', ['plate' => $car->getPlate()]) . '#edit';
                return $this->redirect()->toUrl($url);
            }
        }

        $view = new ViewModel([
            'car' => $car,
            'carForm' => $form,
            'disableInputStatusMaintenance' => $disableInputStatusMaintenance,
            'roles' => $this->roles,
        ]);
        $view->setTerminal(true);
        return $view;
    }

    public function commandsTabAction() {
        $plate = $this->params()->fromRoute('plate', 0);
        $car = $this->carsService->getCarByPlate($plate);
        $commands = Commands::getCommandCodes();
        unset($commands[Commands::CLOSE_TRIP]);
        /*if(count($this->tripsService->getTripsByPlateNotEnded($car->getPlate()))>0){
            unset($commands[Commands::START_TRIP]);
        }*/
        $view = new ViewModel([
            'commands' => $commands,
            'car' => $car,
            'nTripOpen' => count($this->tripsService->getTripsByPlateNotEnded($car->getPlate()))
        ]);
        $view->setTerminal(true);
        return $view;
    }

    public function damagesTabAction() {
        $translator = $this->TranslatorPlugin();
        $plate = $this->params()->fromRoute('plate', 0);
        $car = $this->carsService->getCarByPlate($plate);
        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();
            try {
                if (isset($postData['damages'])) {
                    $this->carsService->updateDamages($car, $postData['damages']);
                } else {
                    $this->carsService->updateDamages($car, null);
                }
                $this->flashMessenger()->addSuccessMessage($translator->translate('Danni auto modificati con successo!'));
            } catch (\Exception $e) {
                $this->flashMessenger()->addErrorMessage($translator->translate('Si è verificato un errore applicativo. L\'assistenza tecnica è già al corrente, ci scusiamo per l\'inconveniente'));
            }

            $url = $this->url()->fromRoute('cars/edit', ['plate' => $car->getPlate()]) . '#damages';
            return $this->redirect()->toUrl($url);
        }

        $view = new ViewModel([
            'damages' => $this->carsService->getDamagesList(),
            'car' => $car,
            'carDamages' => json_decode($car->getDamages(), true)
        ]);
        $view->setTerminal(true);
        return $view;
    }

    public function insuranceTabAction() {
        $translator = $this->TranslatorPlugin();
        $plate = $this->params()->fromRoute('plate', 0);
        $car = $this->carsService->getCarByPlate($plate);
        $carInfo = $car->getCarsInfo();

        $insuranceConfig = $this->getCarsInfoConfiguration();

        if ($this->getRequest()->isPost()) {
            try {
                $postData = $this->getRequest()->getPost()->toArray();

                if(!isset($postData['number'])) {
                    $postData['number'] = $carInfo->getInsuranceNumber();
                }

                foreach ($insuranceConfig as $value) {
                    if ($value->company==$postData['company']) {
                        $postData['number'] = $value->number;
                        break;
                    }
                }

                $this->carsService->updateInsurance($car, $postData['company'], $postData['number'], $postData['valid_from'], $postData['expiry']);
                $this->flashMessenger()->addSuccessMessage($translator->translate('Dati polizza assicurativa modificati con successo!'));

            } catch (\Exception $e) {
                $this->flashMessenger()->addErrorMessage($translator->translate('Si è verificato un errore applicativo. L\'assistenza tecnica è già al corrente, ci scusiamo per l\'inconveniente'));
            }
            $url = $this->url()->fromRoute('cars/edit', ['plate' => $car->getPlate()]) . '#insurance';
            return $this->redirect()->toUrl($url);
        }

        $view = new ViewModel([
            'insuranceConfig' => $insuranceConfig,
            'car' => $car,
            'carInfo' => $carInfo
        ]);
        $view->setTerminal(true);
        return $view;
    }

    public function deleteAction() {
        $translator = $this->TranslatorPlugin();
        $plate = $this->params()->fromRoute('plate', 0);

        /** @var Cars $car */
        $car = $this->carsService->getCarByPlate($plate);

        if (is_null($car)) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);

            return false;
        }

        try {

            $this->carsService->deleteCar($car);
            $this->flashMessenger()->addSuccessMessage($translator->translate('Auto rimossa con successo!'));
        } catch (\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException $e) {

            $this->flashMessenger()->addErrorMessage($translator->translate('L\'auto non può essere rimossa perchè ha effettuato una o più corse.'));
        }

        return $this->redirect()->toRoute('cars');
    }

    public function sendCommandAction() {
        $translator = $this->TranslatorPlugin();
        $plate = $this->params()->fromRoute('plate', 0);
        $commandIndex = $this->params()->fromRoute('command', 0);
        $txtArg1 = trim($this->params()->fromPost('txtArg1') != null ? $this->params()->fromPost('txtArg1') : '');
        
        $car = $this->carsService->getCarByPlate($plate);

        if (is_null($car)) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);
            return false;
        }

        try {
            $this->commandsService->sendCommand($car, $commandIndex, $this->identity(), null, null, $txtArg1);
            $this->flashMessenger()->addSuccessMessage($translator->translate('Comando eseguito con successo'));
        } catch (\Exception $e) {
            $this->flashMessenger()->addErrorMessage($translator->translate('Errore nell\'esecuzione del comando'));
        }
        
        $url = $this->url()->fromRoute('cars/edit', ['plate' => $car->getPlate()]) . '#commands';
        return $this->redirect()->toUrl($url);
    }

    protected function _getRecordsFiltered($as_filters, $i_totalCars) {
        if (empty($as_filters['searchValue']) && !isset($as_filters['columnValueWithoutLike'])) {

            return $i_totalCars;
        } else {

            $as_filters['withLimit'] = false;

            return $this->carsService->getDataDataTable($as_filters, true);
        }
    }
    
    public function locationNotActiveAction(){
        
        $locationNotActive = $this->maintenanceLocationsService->findAllNotActive();
        
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(json_encode($locationNotActive));
        return $response;
    }
    
    public function motivationNotActiveAction(){
        
        $motivationNotActive = $this->maintenanceMotivationsService->findAllNotActive();
        
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(json_encode($motivationNotActive));
        return $response;
    }

    public function blackboxCoordinatesAction(){
        $plate = $this->params()->fromQuery('plate', 0);
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $result = json_encode(["status" => "KO"]);
        $response->setContent($result);

        if(!is_null($plate) && $plate != "") {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://sharengo.kubris.com/service/plateInfo/" . $plate);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($ch);
            $res = json_decode($output, true);
            curl_close($ch);
        }else{
            return $response;
        }

        if(isset($res['data']) && !is_null($res['data'])){
            $positionLinkBlackBox = sprintf(
                'http://maps.google.com/?q=%s,%s',
                $res['data']['geoLatitude'],
                $res['data']['geoLongitude']
            );
        } else {
            return $response;
        }
        $response->setContent(json_encode(["status" => "OK", "link" => $positionLinkBlackBox]));
        return $response;
    }


    private function  getCarsInfoConfiguration()
    {
        $result = [];
        $carConfigurations = $this->configurationsService->getConfigurationsBySlug(Configurations::CAR, false);
        foreach ($carConfigurations as $configuration) {
            if($configuration->getConfigKey()=="cars_info_insurance" && $configuration->getConfigValue()=="true") {
                $result = json_decode($configuration->getConfigSpecific());
            }
        }

        return $result;
    }

}

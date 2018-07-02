<?php
namespace Application\Controller;

// Internals
use Application\Form\InputData\CloseTripDataFactory;
use Application\Form\TripCostForm;
use SharengoCore\Entity\Trips;
use SharengoCore\Exception\InvalidFormInputData;
use SharengoCore\Exception\TripNotFoundException;
use SharengoCore\Service\EventsService;
use SharengoCore\Service\LogsService;
use SharengoCore\Service\TripCostComputerService;
use SharengoCore\Service\TripsService;
use BusinessCore\Service\BusinessService;
use BusinessCore\Service\BusinessTripService;

// Externals
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Zend\EventManager\EventManager;

class TripsController extends AbstractActionController
{
    /**
     * @var TripsService
     */
    private $tripsService;

    /**
     * @var TripCostForm
     */
    private $tripCostForm;

    /**
     * @var TripCostComputerService
     */
    private $tripCostComputerService;

    /**
     * @var EventsService
     */
    private $eventsService;
    
    /**
     * @var EventsService
     */
    private $logsService;

    /**
     * @var EventManager
     */
    private $eventManager;

    /**
     * @var CloseTripDataFactory
     */
    private $closeTripDataFactory;

    /**
     * @var datatableFiltersSessionContainer
     */
    private $datatableFiltersSessionContainer;

    /**
     *
     * @var businessService
     */
    private $businessService;

    /**

    /**
     *
     * @var tripCostService
     */
    private $tripCostService;

    public function __construct(
        TripsService $tripsService,
        TripCostForm $tripCostForm,
        TripCostComputerService $tripCostComputerService,
        EventsService $eventsService,
        LogsService $logsService,
        EventManager $eventManager,
        CloseTripDataFactory $closeTripDataFactory,
        Container $datatableFiltersSessionContainer,
        BusinessService $businessService,
        BusinessTripService $businessTripService
    ) {
        $this->tripsService = $tripsService;
        $this->tripCostForm = $tripCostForm;
        $this->tripCostComputerService = $tripCostComputerService;
        $this->eventsService = $eventsService;
        $this->logsService = $logsService;
        $this->eventManager = $eventManager;
        $this->closeTripDataFactory = $closeTripDataFactory;
        $this->datatableFiltersSessionContainer = $datatableFiltersSessionContainer;
        $this->businessService = $businessService;
        $this->businessTripService = $businessTripService;
    }

    /**
     * This method return an array containing the DataTable filters,
     * from a Session Container.
     *
     * @return array
     */
    private function getDataTableSessionFilters()
    {
        return $this->datatableFiltersSessionContainer->offsetGet('Trips');
    }

    public function indexAction()
    {
        $sessionDatatableFilters = $this->getDataTableSessionFilters();

        return new ViewModel([
            'filters' => json_encode($sessionDatatableFilters),
        ]);
    }

    public function datatableAction()
    {
        $filters = $this->params()->fromPost();
        $filters['withLimit'] = true;
        $dataDataTable = $this->tripsService->getDataDataTable($filters);
        $tripsTotal = $this->tripsService->getTotalTrips();
        $recordsFiltered = $this->getRecordsFiltered($filters, $tripsTotal);

        return new JsonModel(array(
            'draw'            => $this->params()->fromQuery('sEcho', 0),
            'recordsTotal'    => $tripsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $dataDataTable
        ));
    }

    private function getRecordsFiltered($filters, $tripsTotal)
    {
        if (empty($filters['searchValue']) &&
            !isset($filters['columnNull']) &&
            empty($filters['from']) &&
            empty($filters['to'])) {

            return $tripsTotal;
        } else {
            $filters['withLimit'] = false;
            return $this->tripsService->getDataDataTable($filters, true);
        }
    }

    public function tripCostAction()
    {
        return new ViewModel([
            'form' => $this->tripCostForm
        ]);
    }

    public function tripCostComputationAction()
    {
        $tripBeginning = date_create_from_format('Y/m/d H:i:s', $this->params()->fromPost('tripBeginning'));
        $tripEnd = date_create_from_format('Y/m/d H:i:s', $this->params()->fromPost('tripEnd'));
        $tripParkSeconds = (int) $this->params()->fromPost('tripParkSeconds');
        $customerGender = $this->params()->fromPost('customerGender');
        $customerBonus = (int) $this->params()->fromPost('customerBonus');
        $customerDiscount = (int) $this->params()->fromPost('customerDiscount');

        $tripCost = $this->tripCostComputerService->computeCost(
            $tripBeginning,
            $tripEnd,
            $tripParkSeconds,
            $customerGender,
            $customerBonus,
            $customerDiscount
        );

        $tripCostNoDiscount = $this->tripCostComputerService->computeCost(
            $tripBeginning,
            $tripEnd,
            $tripParkSeconds,
            $customerGender,
            $customerBonus,
            0
        );

        return new JsonModel([
            'cost' => $tripCost,
            'costNoDiscount' => $tripCostNoDiscount
        ]);
    }

    public function detailsAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        $trip = $this->tripsService->getTripById($id);
        $tab = $this->params()->fromQuery('tab', 'info');

        return new ViewModel([
            'tripId' => $id,
            'tab'    => $tab,
            'trip'   => $trip,
            'customer' => $trip->getCustomer()
        ]);
    }

    public function infoTabAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);

        $trip = $this->tripsService->getTripById($id);
        $business = $this->tripsService->getBusinessByTrip($trip);

        $view = new ViewModel([
            'trip' => $trip,
            'business' => $business
        ]);
        $view->setTerminal(true);

        return $view;
    }

    public function costTabAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        $trip = $this->tripsService->getTripById($id);

        $businessTripPayment = $this->tripsService->getBusinessTripPayment($trip);
        $businessTripFare = $this->tripsService->getBusinessFareByTrip($trip);
        $businessInvoice = $this->tripsService->getBusinessInvoiceByTrip($trip);

        $view = new ViewModel([
            'trip' => $trip,
            'businessTripPayment' => $businessTripPayment,
            'businessTripFare' => $businessTripFare,
            'businessInvoice' => $businessInvoice
        ]);
        $view->setTerminal(true);

        return $view;
    }

    public function closeTabAction()
    {
        $id = $this->params()->fromRoute('id', 0);

        $trip = $this->tripsService->getTripById($id);

        if (!$trip instanceof Trips) {
            throw new TripNotFoundException();
        }

        $events = $this->eventsService->getEventsByTrip($trip);

        $view = new ViewModel([
            'trip' => $trip,
            'events' => $events
        ]);
        $view->setTerminal(true);

        return $view;
    }

    public function mapTabAction()
    {
        $id = $this->params()->fromRoute('id', 0);

        $trip = $this->tripsService->getTripById($id);

        if (!$trip instanceof Trips) {
            throw new TripNotFoundException();
        }

        $events = $this->eventsService->getEventsByTrip($trip);
        
        $arrayEvent = array();
        $arrayJsonEvents = array();
        foreach ($events as $event){   
            $arrayEvent['id'] = $event->getId();
            $arrayEvent['date'] = $event->getEventTime()->format('d-m-Y H:i:s');
            $arrayEvent['battery'] = $event->getBattery();
            $arrayEvent['km'] = $event->getKm();
            $arrayEvent['eventTypeId'] =  $event->getEventId();
            $arrayEvent['label'] =  ((($event->getEventType()) != null) ? strtoupper($event->getEventType()->getLabel()) : "null" );
            $arrayEvent['textVal'] = $event->getTxtval();
            $arrayEvent['intVal'] = $event->getIntval();
            $arrayEvent['lon'] = $event->getLon();
            $arrayEvent['lat'] = $event->getLat();
            $arrayJsonEvents[] = $arrayEvent;
        }
        
        $logs = $this->logsService->getLogsByTrip($trip);
        
        $arrayLog = array();
        $arrayJsonLogs = array();
        foreach ($logs as $log){   
            $arrayLog['id'] = $log->getId();
            $arrayLog['SOC'] = $log->getSoc();
            $arrayLog['logTime'] = $log->getLogTime()->format('Y-m-d H:i:s');
            $arrayLog['lon'] = $log->getIntLon();
            $arrayLog['lat'] = $log->getIntLat();
            $arrayJsonLogs[] = $arrayLog;
        }
        
        $view = new ViewModel();
        $view->setTemplate('partials/map-trip.phtml');
        $view->setVariables(['eventsJson' => json_encode($arrayJsonEvents)]);//json di eventi
        $view->setVariables(['events' => $events]);//json di eventi
        $view->setVariables(['logsJson' => json_encode($arrayJsonLogs)]);//json di log
        $view->setTerminal(true);

        return $view;
    }

    public function doCloseAction()
    {
        $translator = $this->TranslatorPlugin();
        $data = $this->params()->fromPost();
        
        $trip = $this->tripsService->getTripById($data['id']);
        $webuser = $this->identity();
        
        try {
            if ($webuser->getRole() == 'superadmin' || $trip->getCustomer()->getGoldList() || $trip->getCustomer()->getMaintainer()) {
                $this->closeTrip($data, $translator);
            } else {
                if ($this->tripsService->checkUserModifyTrip($trip->getCustomer()->getId())) {
                    $this->closeTrip($data, $translator);
                } else {
                    $this->flashMessenger()->addErrorMessage($translator->translate('Attenzione: superato il numero massimo (6) di chiusure/modifiche mensili!'));
                }
            }
            return $this->redirect()->toRoute('trips/details', ['id' => $data['id']]);
        } catch (InvalidFormInputData $e) {
            $this->flashMessenger()->addErrorMessage($e->getMessage());

            return $this->redirect()->toRoute('trips/details', ['id' => $data['id']], ['query' => ['tab' => 'close']]);
        }
    }
    
    private function closeTrip($data, $translator) {
        $inputData = $this->closeTripDataFactory->createFromArray($data);

        $this->tripsService->closeTrip($inputData, $this->identity());

        $this->eventManager->trigger('trip-closed', $this, [
            'topic' => 'trips',
            'trip_id' => $data['id'],
            'action' => 'Close trip data: payable ' . ($data['payable'] ? 'true' : 'false') . ', end date ' . $data['datetime']
        ]);

        $this->flashMessenger()->addSuccessMessage($translator->translate('Corsa chiusa con successo'));
    }

}

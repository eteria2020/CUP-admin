<?php
namespace Application\Controller;

// Internals
use Application\Form\InputData\CloseTripDataFactory;
use Application\Form\TripCostForm;
use SharengoCore\Entity\Trips;
//use SharengoCore\Exception\EditTripDeniedException;
//use SharengoCore\Exception\EditTripNotDateTimeException;
//use SharengoCore\Exception\EditTripWrongDateException;
use SharengoCore\Exception\InvalidFormInputData;
use SharengoCore\Exception\TripNotFoundException;
use SharengoCore\Service\EventsService;
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
     *
     * @var businessTripService 
     */
    private $businessTripService;

    public function __construct(
        TripsService $tripsService,
        TripCostForm $tripCostForm,
        TripCostComputerService $tripCostComputerService,
        EventsService $eventsService,
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

        $tripCost = $this->tripCostComputerService->computeCost(
            $tripBeginning,
            $tripEnd,
            $tripParkSeconds,
            $customerGender,
            $customerBonus
        );

        return new JsonModel([
            'cost' => $tripCost
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
        $business = null;

        if(!is_null($trip->getPinType())){
            $businessTrip = $this->businessTripService->getBusinessTripByTripId($trip->getId());
            if(!is_null($businessTrip)){
                $business = $businessTrip->getBusiness();
            }
        }

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

        $view = new ViewModel([
            'trip' => $trip
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

    public function eventsTabAction()
    {
        $id = $this->params()->fromRoute('id', 0);

        $trip = $this->tripsService->getTripById($id);

        if (!$trip instanceof Trips) {
            throw new TripNotFoundException();
        }

        $events = $this->eventsService->getEventsByTrip($trip);

        $view = new ViewModel();
        $view->setTemplate('partials/events-table.phtml');
        $view->setVariables(['events' => $events]);
        $view->setTerminal(true);

        return $view;
    }

    public function doCloseAction()
    {
        $translator = $this->TranslatorPlugin();
        $data = $this->params()->fromPost();

        try {
            $inputData = $this->closeTripDataFactory->createFromArray($data);

            $this->tripsService->closeTrip($inputData, $this->identity());

            $this->eventManager->trigger('trip-closed', $this, [
                'topic' => 'trips',
                'trip_id' => $data['id'],
                'action' => 'Close trip data: payable ' . ($data['payable'] ? 'true' : 'false') . ', end date ' . $data['datetime']
            ]);

            $this->flashMessenger()->addSuccessMessage($translator->translate('Corsa chiusa con successo'));

            return $this->redirect()->toRoute('trips/details', ['id' => $data['id']]);
        } catch (InvalidFormInputData $e) {
            $this->flashMessenger()->addErrorMessage($e->getMessage());

            return $this->redirect()->toRoute('trips/details', ['id' => $data['id']], ['query' => ['tab' => 'close']]);
        }
    }
}

<?php
namespace Application\Controller;

// Internals
use Application\Form\InputData\CloseTripDataFactory;
use Application\Form\TripCostForm;
use SharengoCore\Entity\Trips;
use SharengoCore\Exception\EditTripDeniedException;
use SharengoCore\Exception\EditTripNotDateTimeException;
use SharengoCore\Exception\EditTripWrongDateException;
use SharengoCore\Exception\InvalidFormInputData;
use SharengoCore\Exception\TripNotFoundException;
use SharengoCore\Service\EventsService;
use SharengoCore\Service\EventsTypesService;
use SharengoCore\Service\TripCostComputerService;
use SharengoCore\Service\TripsService;
// Externals
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

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
     * @var EventsTypesService
     */
    private $eventsTypesService;

    /**
     * @var CloseTripDataFactory
     */
    private $closeTripDataFactory;

    /**
     * @var Container
     */
    private $datatableFiltersSessionContainer;

    public function __construct(
        TripsService $tripsService,
        TripCostForm $tripCostForm,
        TripCostComputerService $tripCostComputerService,
        EventsService $eventsService,
        EventsTypesService $eventsTypesService,
        CloseTripDataFactory $closeTripDataFactory,
        Container $datatableFiltersSessionContainer
    ) {
        $this->tripsService = $tripsService;
        $this->tripCostForm = $tripCostForm;
        $this->tripCostComputerService = $tripCostComputerService;
        $this->eventsService = $eventsService;
        $this->eventsTypesService = $eventsTypesService;
        $this->closeTripDataFactory = $closeTripDataFactory;
        $this->datatableFiltersSessionContainer = $datatableFiltersSessionContainer;
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
        if (empty($filters['searchValue']) && !isset($filters['columnNull'])) {
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

        $view = new ViewModel([
            'trip' => $trip
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

        foreach ($events as $event) {
            $eventType = $this->eventsTypesService->mapEvent($event);
            $event->setEventType($eventType);
        }

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

        foreach ($events as $event) {
            $eventType = $this->eventsTypesService->mapEvent($event);
            $event->setEventType($eventType);
        }

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

            $this->flashMessenger()->addSuccessMessage($translator->translate('Corsa chiusa con successo'));

            return $this->redirect()->toRoute('trips/details', ['id' => $data['id']]);
        } catch (InvalidFormInputData $e) {
            $this->flashMessenger()->addErrorMessage($e->getMessage());

            return $this->redirect()->toRoute('trips/details', ['id' => $data['id']], ['query' => ['tab' => 'close']]);
        }
    }
}
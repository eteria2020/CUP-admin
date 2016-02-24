<?php
namespace Application\Controller;

use Application\Form\InputData\CloseTripDataFactory;
use Application\Form\TripCostForm;
use SharengoCore\Entity\Trips;
use SharengoCore\Exception\EditTripDeniedException;
use SharengoCore\Exception\EditTripNotDateTimeException;
use SharengoCore\Exception\EditTripWrongDateException;
use SharengoCore\Exception\InvalidFormInputData;
use SharengoCore\Exception\TripNotFoundException;
use SharengoCore\Service\EventsService;
use SharengoCore\Service\TripCostComputerService;
use SharengoCore\Service\TripsService;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

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
     * @var CloseTripDataFactory
     */
    private $closeTripDataFactory;

    public function __construct(
        TripsService $tripsService,
        TripCostForm $tripCostForm,
        TripCostComputerService $tripCostComputerService,
        EventsService $eventsService,
        CloseTripDataFactory $closeTripDataFactory
    ) {
        $this->tripsService = $tripsService;
        $this->tripCostForm = $tripCostForm;
        $this->tripCostComputerService = $tripCostComputerService;
        $this->eventsService = $eventsService;
        $this->closeTripDataFactory = $closeTripDataFactory;
    }

    public function indexAction()
    {
        return new ViewModel();
    }

    public function datatableAction()
    {
        $as_filters = $this->params()->fromPost();
        $as_filters['withLimit'] = true;
        $as_dataDataTable = $this->tripsService->getDataDataTable($as_filters);
        $i_tripsTotal = $this->tripsService->getTotalTrips();
        $i_recordsFiltered = $this->_getRecordsFiltered($as_filters, $i_tripsTotal);

        return new JsonModel(array(
            'draw'            => $this->params()->fromQuery('sEcho', 0),
            'recordsTotal'    => $i_tripsTotal,
            'recordsFiltered' => $i_recordsFiltered,
            'data'            => $as_dataDataTable
        ));
    }

    protected function _getRecordsFiltered($as_filters, $i_tripsTotal)
    {
        if (empty($as_filters['searchValue']) && !isset($as_filters['columnNull'])) {
            return $i_tripsTotal;
        } else {
            $as_filters['withLimit'] = false;
            return $this->tripsService->getDataDataTable($as_filters, true);
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

        $view = new ViewModel([
            'trip' => $trip,
            'events' => $events
        ]);
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

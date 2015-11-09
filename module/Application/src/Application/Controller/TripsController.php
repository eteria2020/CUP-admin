<?php
namespace Application\Controller;

use Application\Form\TripCostForm;
use Application\Form\EditTripForm;
use SharengoCore\Service\TripsService;
use SharengoCore\Service\TripCostComputerService;
use SharengoCore\Service\EventsService;
use SharengoCore\Service\EditTripsService;
use SharengoCore\Entity\TripPayments;
use SharengoCore\Entity\Invoices;
use SharengoCore\Exception\EditTripDeniedException;
use SharengoCore\Exception\EditTripWrongDateException;
use SharengoCore\Exception\EditTripNotDateTimeException;
use SharengoCore\Entity\Trips;
use SharengoCore\Exception\TripNotFoundException;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\EventManager\EventManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

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
     * @var EditTripsService
     */
    private $editTripsService;

    /**
     * @var EditTripForm
     */
    private $editTripForm;

    /**
     * @var DoctrineHydrator
     */
    private $hydrator;

    /**
     * @var EventManager
     */
    private $eventManager;

    public function __construct(
        TripsService $tripsService,
        TripCostForm $tripCostForm,
        TripCostComputerService $tripCostComputerService,
        EventsService $eventsService,
        EditTripsService $editTripsService,
        EditTripForm $editTripForm,
        EventManager $eventManager,
        DoctrineHydrator $hydrator
    ) {
        $this->tripsService = $tripsService;
        $this->tripCostForm = $tripCostForm;
        $this->tripCostComputerService = $tripCostComputerService;
        $this->eventsService = $eventsService;
        $this->editTripsService = $editTripsService;
        $this->editTripForm = $editTripForm;
        $this->eventManager = $eventManager;
        $this->hydrator = $hydrator;
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

            return count($this->tripsService->getDataDataTable($as_filters));
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

    public function editTabAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);

        $trip = $this->tripsService->getTripById($id);

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();

            try {
                $this->editTripsService->editTrip(
                    $trip,
                    !$postData['trip']['payable'],
                    date_create_from_format('d-m-Y H:i:s', $postData['trip']['timestampEnd'])
                );

                $this->eventManager->trigger('trip-edited', $this, [
                    'topic' => 'trips',
                    'trip_id' => $trip->getId(),
                    'action' => 'Edit trip data: payable ' . ($postData['trip']['payable'] ? 'true' : 'false') . ', end date ' . $postData['trip']['timestampEnd']
                ]);

                $this->flashMessenger()->addSuccessMessage('Modifica effettuta con successo!');
            } catch (EditTripDeniedException $e) {
                $this->flashMessenger()->addErrorMessage('La corsa non può essere modificata perché non è conclusa o il processo di pagamento è già iniziato.');
            } catch (EditTripWrongDateException $e) {
                $this->flashMessenger()->addErrorMessage('La data specificata non può essere precedente alla data di inizio della corsa');
            } catch (EditTripNotDateTimeException $e) {
                $this->flashMessenger()->addErrorMessage('La data specificata non è nel formato corretto. Verifica i dati inseriti.');
            } catch (\Exception $e) {
                $this->flashMessenger()->addErrorMessage('Si è verificato un errore applicativo. L\'assistenza tecnica è già al corrente, ci scusiamo per l\'inconveniente');
            }

            return $this->redirect()->toUrl('/trips/details/' . $trip->getId() . '?tab=edit');
        }

        $events = $this->eventsService->getEventsByTrip($trip);

        $tripArray = $trip->toArray($this->hydrator, []);

        if ($trip->isEnded()) {
            $tripArray['timestampEnd'] = $tripArray['timestampEnd']->format('d-m-Y H:i:s');
        }

        $this->editTripForm->setData(['trip' => $tripArray]);

        $view = new ViewModel([
            'trip' => $trip,
            'events' => $events,
            'editTripForm' => $this->editTripForm
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

        $view = new ViewModel([
            'trip' => $trip
        ]);
        $view->setTerminal(true);

        return $view;
    }

    public function doCloseAction()
    {
        $tripId = $this->params()->fromPost('id');

        return $this->redirect()->toRoute('trips/details', ['id' => $tripId], ['query' => ['tab' => 'close']]);
    }
}

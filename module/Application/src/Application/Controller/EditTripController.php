<?php
namespace Application\Controller;

use Application\Form\EditTripForm;
use SharengoCore\Entity\Trips;
use SharengoCore\Exception\EditTripDeniedException;
use SharengoCore\Exception\EditTripNotDateTimeException;
use SharengoCore\Exception\EditTripWrongDateException;
use SharengoCore\Exception\TripNotFoundException;
use SharengoCore\Service\EditTripsService;
use SharengoCore\Service\EventsService;
use SharengoCore\Service\TripsService;
use SharengoCore\Service\PaymentScriptRunsService;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\EventManager\EventManager;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class EditTripController extends AbstractActionController
{
    /**
     * @var TripsService
     */
    private $tripsService;

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

    /**
     * @var PaymentScriptRunsService
     */
    private $paymentScriptRunsService;

    /**
     * @param TripsService $tripsService
     * @param EditTripsService $editTripsService
     * @param EventManager $eventManager
     * @param EventsService $eventsService
     * @param DoctrineHydrator $hydrator
     * @param EditTripForm $editTripForm
     * @param PaymentScriptRunsService $paymentScriptRunsService
     */
    public function __construct(
        TripsService $tripsService,
        EditTripsService $editTripsService,
        EventManager $eventManager,
        EventsService $eventsService,
        DoctrineHydrator $hydrator,
        EditTripForm $editTripForm,
        PaymentScriptRunsService $paymentScriptRunsService
    ) {
        $this->tripsService = $tripsService;
        $this->eventsService = $eventsService;
        $this->editTripsService = $editTripsService;
        $this->editTripForm = $editTripForm;
        $this->eventManager = $eventManager;
        $this->hydrator = $hydrator;
        $this->paymentScriptRunsService = $paymentScriptRunsService;
    }

    public function editTabAction()
    {
        $translator = $this->TranslatorPlugin();
        $id = (int)$this->params()->fromRoute('id', 0);
        $webuser = $this->identity();
        $trip = $this->tripsService->getTripById($id);

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();

            try {
                $this->editTripsService->editTrip(
                    $trip,
                    !$postData['trip']['payable'],
                    date_create_from_format('d-m-Y H:i:s', $postData['trip']['timestampEnd']),
                    $webuser
                );

                $this->eventManager->trigger('trip-edited', $this, [
                    'topic' => 'trips',
                    'trip_id' => $trip->getId(),
                    'action' => 'Edit trip data: payable ' . ($postData['trip']['payable'] ? 'true' : 'false') . ', end date ' . $postData['trip']['timestampEnd']
                ]);

                $this->flashMessenger()->addSuccessMessage($translator->translate('Modifica effettuta con successo!'));
            } catch (EditTripDeniedException $e) {
                $this->flashMessenger()->addErrorMessage($translator->translate('La corsa non può essere modificata perché non è conclusa.'));
            } catch (EditTripWrongDateException $e) {
                $this->flashMessenger()->addErrorMessage($translator->translate('La data specificata non può essere precedente alla data di inizio della corsa'));
            } catch (EditTripNotDateTimeException $e) {
                $this->flashMessenger()->addErrorMessage($translator->translate('La data specificata non è nel formato corretto. Verifica i dati inseriti.'));
            } catch (\Exception $e) {
                $this->flashMessenger()->addErrorMessage($e->getMessage());
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
            'editTripForm' => $this->editTripForm,
            'scriptRunning' => $this->paymentScriptRunsService->isScriptRunning()
        ]);
        $view->setTerminal(true);

        return $view;
    }
}

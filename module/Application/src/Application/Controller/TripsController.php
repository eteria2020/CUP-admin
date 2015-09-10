<?php
namespace Application\Controller;

use Application\Form\TripCostForm;
use SharengoCore\Service\TripsService;
use SharengoCore\Service\TripCostComputerService;
use SharengoCore\Service\TripPaymentsService;
use SharengoCore\Entity\TripPayments;
use SharengoCore\Entity\Invoices;

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
     * @var TripPaymentsService
     */
    private $tripPaymentsService;

    public function __construct(
        TripsService $tripsService,
        TripCostForm $tripCostForm,
        TripCostComputerService $tripCostComputerService,
        TripPaymentsService $tripPaymentsService
    ) {
        $this->tripsService = $tripsService;
        $this->tripCostForm = $tripCostForm;
        $this->tripCostComputerService = $tripCostComputerService;
        $this->tripPaymentsService = $tripPaymentsService;
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
        $tab = $this->params()->fromQuery('tab', 'info');

        return new ViewModel([
            'tripId' => $id,
            'tab'      => $tab
        ]);
    }

    public function infoTabAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);

        $trip = $this->tripsService->getTripById($id);
        $tripPayment = $this->tripPaymentsService->getTripPaymentForTrip($trip);

        $view = new ViewModel([
            'trip' => $trip,
            'tripPayment' => $tripPayment
        ]);
        $view->setTerminal(true);

        return $view;
    }

    public function costTabAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);

        $view = new ViewModel([
            'trip' => $trip
        ]);
        $view->setTerminal(true);

        return $view;
    }
}

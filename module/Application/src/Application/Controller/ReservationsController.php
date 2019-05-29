<?php
namespace Application\Controller;

// Internals
use SharengoCore\Service\ReservationsService;
// Externals
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

class ReservationsController extends AbstractActionController
{
    /**
     * @var ReservationsService
     */
    public $reservationsService;

    /**
     * @var Container
     */
    private $datatableFiltersSessionContainer;

    public function __construct(
        ReservationsService $reservationsService,
        Container $datatableFiltersSessionContainer
    ) {
        $this->reservationsService = $reservationsService;
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
        return $this->datatableFiltersSessionContainer->offsetGet('Reservations');
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
        $dataDataTable = $this->reservationsService->getDataDataTable($filters);
        $totalReservations = $this->reservationsService->getTotalReservations();
        $recordsFiltered = $this->getRecordsFiltered($filters, $totalReservations);

        return new JsonModel([
            'draw'            => $this->params()->fromQuery('sEcho', 0),
            'recordsTotal'    => $totalReservations,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $dataDataTable
        ]);
    }

    private function getRecordsFiltered($filters, $totalReservations)
    {
        $filters['withLimit'] = false;

        if (isset($filters['columnFromDate']) && isset($filters['columnFromEnd'])) {

            return $this->reservationsService->getDataDataTable($filters, true);

        } elseif (empty($filters['searchValue'])) {

            return $totalReservations;

        } else {

            return $this->reservationsService->getDataDataTable($filters, true);
        }
    }
}

<?php
namespace Application\Controller;

// Internals
use SharengoCore\Service\ReservationsService;
// Externals
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class ReservationsController extends AbstractActionController
{
    /**
     * @var ReservationsService
     */
    public $reservationsService;

    public function __construct(ReservationsService $reservationsService)
    {
        $this->reservationsService = $reservationsService;
    }

    public function indexAction()
    {
        $sessionDatatableFilters = $this->reservationsService->getDataTableSessionFilters();

        return new ViewModel([
            'filters' => $sessionDatatableFilters,
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

        } else if (empty($filters['searchValue'])) {

            return $totalReservations;

        } else {

            return $this->reservationsService->getDataDataTable($filters, true);
        }
    }
}

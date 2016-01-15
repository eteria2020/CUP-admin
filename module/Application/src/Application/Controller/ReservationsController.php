<?php
namespace Application\Controller;

use SharengoCore\Service\ReservationsService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class ReservationsController extends AbstractActionController
{
    /**
     * @var ReservationsService
     */
    public $I_reservationsService;

    public function __construct(ReservationsService $I_reservationsService)
    {
        $this->I_reservationsService = $I_reservationsService;
    }

    public function indexAction()
    {
        return new ViewModel([]);
    }

    public function datatableAction()
    {
        $as_filters = $this->params()->fromPost();
        $as_filters['withLimit'] = true;
        $as_dataDataTable = $this->I_reservationsService->getDataDataTable($as_filters);
        $i_totalReservations = $this->I_reservationsService->getTotalReservations();
        $i_recordsFiltered = $this->_getRecordsFiltered($as_filters, $i_totalReservations);

        return new JsonModel([
            'draw'            => $this->params()->fromQuery('sEcho', 0),
            'recordsTotal'    => $i_totalReservations,
            'recordsFiltered' => $i_recordsFiltered,
            'data'            => $as_dataDataTable
        ]);
    }

    protected function _getRecordsFiltered($as_filters, $i_totalReservations)
    {
        $as_filters['withLimit'] = false;

        if (isset($as_filters['columnFromDate']) && isset($as_filters['columnFromEnd'])) {

            return $this->I_reservationsService->getDataDataTable($as_filters, true);

        } else if (empty($as_filters['searchValue'])) {

            return $i_totalReservations;

        } else {

            return $this->I_reservationsService->getDataDataTable($as_filters, true);
        }
    }
}

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
        //$i_recordsFiltered = $this->_getRecordsFiltered($as_filters, $i_userCar);

        return new JsonModel([
            'draw'            => $this->params()->fromQuery('sEcho', 0),
            'recordsTotal'    => $i_totalReservations,
            'recordsFiltered' => 1,
            'data'            => $as_dataDataTable
        ]);
    }
}
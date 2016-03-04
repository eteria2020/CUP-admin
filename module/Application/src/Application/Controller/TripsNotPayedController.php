<?php
namespace Application\Controller;

use SharengoCore\Service\TripsService;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class TripsNotPayedController extends AbstractActionController
{
    /**
     * @var TripsService
     */
    private $tripsService;

    /**
     * @param TripsService $tripsService
     */
    public function __construct(TripsService $tripsService)
    {
        $this->tripsService = $tripsService;
    }

    /**
     * @return JsonModel
     */
    public function datatableAction()
    {
        $as_filters = $this->params()->fromPost();
        $as_filters['withLimit'] = true;
        $as_dataDataTable = $this->tripsService->getDataNotPayedDataTable($as_filters);
        $i_tripsTotal = $this->tripsService->getTotalTripsNotPayed();
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
            return $this->tripsService->getDataNotPayedDataTable($as_filters, true);
        }
    }

    public function listAction()
    {
        return new ViewModel();
    }
}

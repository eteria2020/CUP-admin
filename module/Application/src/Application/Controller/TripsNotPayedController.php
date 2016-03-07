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
        $filters = $this->params()->fromPost();
        $filters['withLimit'] = true;
        $dataDataTable = $this->tripsService->getDataNotPayedDataTable($filters);
        $tripsTotal = $this->tripsService->getTotalTripsNotPayed();
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
            return $this->tripsService->getDataNotPayedDataTable($filters, true);
        }
    }

    public function listAction()
    {
        return new ViewModel();
    }
}

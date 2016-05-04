<?php
namespace Application\Controller;

// Internals
use SharengoCore\Service\TripsService;
// Externals
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

class TripsNotPayedController extends AbstractActionController
{
    /**
     * @var TripsService
     */
    private $tripsService;

    /**
     * @var Container
     */
    private $datatableFiltersSessionContainer;

    /**
     * @param TripsService $tripsService
     * @param Container $datatableFiltersSessionContainer
     */
    public function __construct(
        TripsService $tripsService,
        Container $datatableFiltersSessionContainer
    ) {
        $this->tripsService = $tripsService;
        $this->datatableFiltersSessionContainer = $datatableFiltersSessionContainer;
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

    /**
     * This method return an array containing the DataTable filters,
     * from a Session Container.
     *
     * @return array
     */
    private function getDataTableSessionFilters()
    {
        return $this->datatableFiltersSessionContainer->offsetGet('TripsNotPayed');
    }

    public function listAction()
    {
        $sessionDatatableFilters = $this->getDataTableSessionFilters();

        return new ViewModel([
            'filters' => json_encode($sessionDatatableFilters),
        ]);
    }
}

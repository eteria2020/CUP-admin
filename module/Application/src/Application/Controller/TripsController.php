<?php
namespace Application\Controller;

use Application\Form\CustomerForm;
use SharengoCore\Entity\Customers;
use SharengoCore\Service\CustomersService;
use SharengoCore\Service\TripsService;
use Zend\Form\Form;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class TripsController extends AbstractActionController
{
    /**
     * @var TripsService
     */
    private $I_tripsService;

    public function __construct(TripsService $I_tripsService)
    {
        $this->I_tripsService = $I_tripsService;
    }

    public function indexction()
    {
        return new ViewModel();
    }

    public function datatableAction()
    {
        $as_filters = $this->params()->fromPost();
        $as_filters['withLimit'] = true;
        $as_dataDataTable = $this->I_tripsService->getDataDataTable($as_filters);
        $i_tripsTotal = $this->I_tripsService->getTotalTrips();
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

            return count($this->I_tripsService->getDataDataTable($as_filters));
        }
    }
}
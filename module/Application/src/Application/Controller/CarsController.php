<?php
namespace Application\Controller;

use SharengoCore\Service\CarsService;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class CarsController extends AbstractActionController
{
    /**
     * @var CarsService
     */
    public $I_carsService;

    public function __construct(CarsService $I_carsService)
    {
        $this->I_carsService = $I_carsService;
    }

    public function indexAction()
    {
        return new ViewModel([]);
    }

    public function datatableAction()
    {
        $as_filters = $this->params()->fromPost();
        $as_filters['withLimit'] = true;
        $as_dataDataTable = $this->I_carsService->getDataDataTable($as_filters);
        $i_userTotal = $this->I_carsService->getTotalCars();

        return new JsonModel(array(
            'draw'            => $this->params()->fromQuery('sEcho', 0),
            'recordsTotal'    => $i_userTotal,
            'recordsFiltered' => 1,
            'data'            => $as_dataDataTable
        ));
    }
}
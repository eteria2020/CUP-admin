<?php

namespace Application\Controller;

// Internals
use Application\Form\FaresForm;
use SharengoCore\Service\FaresService;
use SharengoCore\Service\FinesService;
use SharengoCore\Service\PaymentsService;
use SharengoCore\Service\CustomersService;
use SharengoCore\Service\ExtraPaymentsService;
use Cartasi\Service\CartasiContractsService;
use Cartasi\Service\CartasiCustomerPayments;
use SharengoCore\Service\PenaltiesService;
use SharengoCore\Exception\FleetNotFoundException;
use SharengoCore\Service\FleetService;
use SharengoCore\Service\RecapService;
// Externals
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

class FinesController extends AbstractActionController
{
    /**
     * @var finesService
     */
    private $finesService;

    /**
     * @var PaymentsService
     */
    private $paymentsService;

    /**
     * @var CustomersService
     */
    private $customersService;

    /**
     * @var CartasiContractsService
     */
    private $cartasiContractsService;

    /**
     * @var CartasiCustomerPayments
     */
    private $cartasiCustomerPayments;

    /**
     * @var ExtraPaymentsService
     */
    private $extraPaymentsService;

    /**
     * @var PenaltiesService
     */
    private $penaltiesService;

    /**
     * @var FleetService
     */
    private $fleetService;

    /**
     * @var RecapService
     */
    private $recapService;

    /**
     * @var FaresService
     */
    private $faresService;

    /**
     * @var FaresForm;
     */
    private $faresForm;

    /**
     * @var Container
     */
    private $datatableFiltersSessionContainer;

    public function __construct(
        FinesService $finesService,
        PaymentsService $paymentsService,
        CustomersService $customersService,
        CartasiContractsService $cartasiContractsService,
        CartasiCustomerPayments $cartasiCustomerPayments,
        ExtraPaymentsService $extraPaymentsService,
        PenaltiesService $penaltiesService,
        FleetService $fleetService,
        RecapService $recapService,
        FaresService $faresService,
        FaresForm $faresForm,
        Container $datatableFiltersSessionContainer
    ) {
        $this->finesService = $finesService;
        $this->paymentsService = $paymentsService;
        $this->customersService = $customersService;
        $this->cartasiContractsService = $cartasiContractsService;
        $this->cartasiCustomerPayments = $cartasiCustomerPayments;
        $this->extraPaymentsService = $extraPaymentsService;
        $this->penaltiesService = $penaltiesService;
        $this->fleetService = $fleetService;
        $this->recapService = $recapService;
        $this->faresService = $faresService;
        $this->faresForm = $faresForm;
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
        return $this->datatableFiltersSessionContainer->offsetGet('SafoPenalty');
    }

    public function indexAction()
    {
        $sessionDatatableFilters = $this->getDataTableSessionFilters();
        
        if(isset($sessionDatatableFilters['searchValue'])&&($sessionDatatableFilters['searchValue']!="")){
            if($sessionDatatableFilters['column']=="e.vehicleFleetId"){
                $fleets = $this->fleetService->getFleetsSelectorArray();
                $sessionDatatableFilters['searchValue']=$fleets[$sessionDatatableFilters['searchValue']];
            }
        }
        
        return new ViewModel([
            'filters' => json_encode($sessionDatatableFilters),
        ]);
    }

    public function datatableAction()
    {
        $filters = $this->params()->fromPost();
        $filters['withLimit'] = true;

        if($filters['column'] == "" && isset($filters['columnValueWithoutLike']) && $filters['columnValueWithoutLike'] == ""){
            $filters['columnWithoutLike'] = true;
            $filters['columnValueWithoutLike'] = null;
        }


        $dataDataTable = $this->finesService->getFinesData($filters);
        $totalFailedPayments = $this->finesService->getTotalFines();
        $recordsFiltered = $this->getRecordsFiltered($filters, $totalFailedPayments);

        return new JsonModel([
            'draw'            => $this->params()->fromQuery('sEcho', 0),
            'recordsTotal'    => $totalFailedPayments,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $dataDataTable
        ]);
    }

    protected function getRecordsFiltered($filters, $totalTripPayments)
    {
        if (empty($filters['searchValue']) && !isset($filters['columnValueWithoutLike'])) {
            return $totalTripPayments;
        } else {
            $filters['withLimit'] = false;

            return count($this->finesService->getFinesData($filters));
        }
    }

    public function detailsAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);

        $safoPenalty = $this->finesService->getSafoPenaltyById($id);

        return new ViewModel([
            'safoPenalty' => $safoPenalty
        ]);
    }
    
    public function payAction()
    {
        $safoPenalty = array();
        $checkPost = $this->params()->fromPost('check');
        if(isset($checkPost)){
            for($x=0;$x<count($checkPost);$x++){
                $fineObj = $this->finesService->getSafoPenaltyById($checkPost[$x]);
                ($fineObj ? $safoPenalty[$x] = $fineObj : $safoPenalty[$x] = null );
            }
        }

        //$safoPenalty = $this->finesService->getSafoPenaltyById($id);

        return new ViewModel([
            'safoPenalty' => $safoPenalty
        ]);
    }

}

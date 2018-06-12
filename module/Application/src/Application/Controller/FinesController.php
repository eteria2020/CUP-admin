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
use SharengoCore\Entity\ExtraPayments;
// Externals
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

class FinesController extends AbstractActionController
{
    /**
     * @var FinesService
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
        //$filters['withLimit'] = false;
        $filters['withLimit'] = true;

        if($filters['column'] == "" && isset($filters['columnValueWithoutLike']) && $filters['columnValueWithoutLike'] == ""){
            $filters['columnWithoutLike'] = true;
            $filters['columnValueWithoutLike'] = null;
        }

        //$dataDataTable = array_slice($this->filterFinesComplete($this->finesService->getFinesData($filters)), (int)$filters['iDisplayStart'], (int)$filters['iDisplayLength']);
        $dataDataTable = $this->finesService->getFinesData($filters);
        $totalFailedPayments = $this->finesService->getTotalFinesComplete();
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
            //return count($this->filterFinesComplete($this->finesService->getFinesData($filters)));
            return count($this->finesService->getFinesData($filters));
        }
    }
    /*
    private function filterFinesComplete($fines){
        $result = array();
        foreach ($fines as $record){
            if($record['fines']['complete'] == 1 && isset($record['fines']['customerId']) && isset($record['fines']['tripId'])){
                $result[] = $record;
            }
        }
        return $result;
    }
*/
    public function detailsAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);

        $safoPenalty = $this->finesService->getSafoPenaltyById($id);

        return new ViewModel([
            'safoPenalty' => $safoPenalty
        ]);
    }
    
    public function findFinesBetweenDateAction(){
        $from = new \DateTime($this->params()->fromPost('from'));
        $to = $this->params()->fromPost('to') != "" ? new \DateTime($this->params()->fromPost('to')) : new \DateTime();
        
        $fines = $this->finesService->getFinesBetweenDate($from->format('Y-m-d H:i:s'), $to->format('Y-m-d H:i:s'));

        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(json_encode(array ('fine' => array_slice($fines, 0, 50), 'nTotal' => array('nTotalFines' => count($fines)))));
        return $response;
    }
    
    public function payAction()
    {
        try {
            $checkPost = $this->params()->fromPost('check');
            $penalty = $this->penaltiesService->findById(1);
            $c_success = 0;
            $c_fail = 0;
            if (isset($checkPost)) {
                foreach ($checkPost as $fines_id) {
                    $fine = $this->finesService->getSafoPenaltyById($fines_id);
                    $resp = $this->cartasiCustomerPayments->sendPaymentRequest($fine->getCustomer(), $penalty->getAmount());
                    $extraPayment = $this->finesService->createExtraPayment($fine, $penalty, $resp->getTransaction());
                    if (!$resp->getCompletedCorrectly()){
                        $extraPaymentTry = $this->extraPaymentsService->processWrongPayment($extraPayment, $resp);
                        $c_fail ++;
                    } else {
                        $extraPaymentTry = $this->extraPaymentsService->processPayedCorrectly($extraPayment, $resp);
                        $c_success ++;
                    }
                    $this->finesService->clearEntityManager();
                }
            }

            $response = $this->getResponse();
            $response->setStatusCode(200);
            $response->setContent(json_encode(array('n_success' => $c_success, 'n_fail' => $c_fail)));
            return $response;
        } catch (Exception $e) {
            $response = $this->getResponse();
            $response->setStatusCode(200);
            $response->setContent(json_encode(array('error' => true)));
            return $response;
        }
    }
    
}

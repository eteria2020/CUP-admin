<?php
namespace Application\Controller;

use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

use SharengoCore\Service\InvoicesService;

class InvoicesController extends AbstractActionController
{
    /**
     * @var InvoicesService
     */
    private $invoicesService;

    public function __construct(
        InvoicesService $invoicesService
    ) {
        $this->invoicesService = $invoicesService;
    }

    public function indexAction()
    {
        $sessionDatatableFilters = $this->invoicesService->getDataTableSessionFilters();

        return new ViewModel([
            'filters' => json_encode($sessionDatatableFilters),
        ]);
    }

    public function datatableAction()
    {
        $filters = $this->params()->fromPost();
        $filters['withLimit'] = true;
        // get data for datatable from service
        $dataDataTable = $this->invoicesService->getDataDataTable($filters);
        $totalInvoices = $this->invoicesService->getTotalDatatableInvoices($filters);
        $recordsFiltered = $this->getRecordsFiltered($filters, $totalInvoices);

        return new JsonModel([
            'draw'            => $this->params()->fromQuery('sEcho', 0),
            'recordsTotal'    => $totalInvoices,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $dataDataTable
        ]);
    }

    protected function getRecordsFiltered($filters, $totalInvoices)
    {
        if (empty($filters['searchValue']) && !isset($filters['columnValueWithoutLike'])) {

            return $totalInvoices;

        } else {

            $filters['withLimit'] = false;

            return $this->invoicesService->getDataDataTable($filters, true);
        }
    }
}

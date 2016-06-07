<?php
namespace Application\Controller;

use Application\Entity\Webuser;
use Application\Form\UserForm;
use Application\Form\Validator\DuplicateEmail;
use SharengoCore\Service\ZonesService;
use Zend\Form\Form;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;


class ZonesController extends AbstractActionController
{
    /**
     * @var ZoneService
     */
    private $zoneService;

    /**
     */
    public function __construct(ZonesService $zoneService)
    {
        $this->zoneService = $zoneService;
    }

    public function indexAction()
    {
        return new ViewModel();
    }

    public function tripTabAction()
    {
        $view = new ViewModel([
            'list' => $this->zoneService->getListZones()
        ]);
        $view->setTerminal(true);

        return $view;
    }

    public function zoneAlarmsAction()
    {
        $view = new ViewModel([
            'list' => $this->zoneService->getListZonesAlarms()
        ]);

        return $view;
    }

    public function groupsTabAction()
    {
        $view = new ViewModel([
            'list' => $this->zoneService->getListZonesGroups()
        ]);
        $view->setTerminal(true);

        return $view;
    }

    public function pricesTabAction()
    {
        $view = new ViewModel([
            'list' => $this->zoneService->getListZonesPrices()
        ]);
        $view->setTerminal(true);

        return $view;
    }

    public function datatableAction()
    {
        $as_filters = $this->params()->fromPost();
        $as_filters['withLimit'] = true;
        $as_dataDataTable = $this->zoneService->getDataDataTable($as_filters);
        $i_totalZones = $this->zoneService->getTotalZones();
        $i_recordsFiltered = $this->getRecordsFiltered($as_filters, $i_totalZones);

        return new JsonModel([
            'draw'            => $this->params()->fromQuery('sEcho', 0),
            'recordsTotal'    => $i_totalZones,
            'recordsFiltered' => $i_recordsFiltered,
            'data'            => $as_dataDataTable
        ]);
    }

    protected function getRecordsFiltered($as_filters, $i_totalZones)
    {
        if (empty($as_filters['searchValue']) && !isset($as_filters['columnValueWithoutLike'])) {
            return $i_totalZones;
        } else {
            $as_filters['withLimit'] = false;
            return $this->zoneService->getDataDataTable($as_filters, true);
        }
    }

}

<?php
namespace Application\Controller;

// Internals
use Application\Form\ZoneForm;
use SharengoCore\Service\ZonesService;
use SharengoCore\Service\PostGisService;
// Externals
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Session\Container;

class ZonesController extends AbstractActionController
{
    /**
     * @var ZonesService
     */
    private $zonesService;

    /**
     * @var PostGisService
     */
    private $postGisService;
    
    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var ZoneForm
     */
    private $zoneForm;

    /**
     * @var Container
     */
    private $datatableFiltersSessionContainer;

    public function __construct(
        ZonesService $zonesService,
        PostGisService $postGisService,
        ZoneForm $zoneForm,
        HydratorInterface $hydrator,
        Container $datatableFiltersSessionContainer
    ) {
        $this->zonesService = $zonesService;
        $this->postGisService = $postGisService;
        $this->zoneForm = $zoneForm;
        $this->hydrator = $hydrator;
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
        return $this->datatableFiltersSessionContainer->offsetGet('Zone');
    }

    public function indexAction()
    {
        return new ViewModel();
    }

    public function listTabAction()
    {
        $sessionDatatableFilters = $this->getDataTableSessionFilters();

        $view = new ViewModel([
            'filters' => json_encode($sessionDatatableFilters),
        ]);

        $view->setTerminal(true);

        return $view;
    }

    public function zoneAlarmsAction()
    {
        $view = new ViewModel([
            'list' => $this->zonesService->getListZonesAlarms()
        ]);

        return $view;
    }

    public function groupsTabAction()
    {
        $view = new ViewModel([
            'list' => $this->zonesService->getListZonesGroups()
        ]);
        $view->setTerminal(true);

        return $view;
    }

    public function pricesTabAction()
    {
        $view = new ViewModel([
            'list' => $this->zonesService->getListZonesPrices()
        ]);
        $view->setTerminal(true);

        return $view;
    }

    public function datatableAction()
    {
        $filters = $this->params()->fromPost();
        $filters['withLimit'] = true;
        $dataDataTable = $this->zonesService->getDataDataTable($filters);
        $totalZones = $this->zonesService->getTotalZones();
        $recordsFiltered = $this->getRecordsFiltered($filters, $totalZones);

        return new JsonModel([
            'draw' => $this->params()->fromQuery('sEcho', 0),
            'recordsTotal' => $totalZones,
            'recordsFiltered' => $recordsFiltered,
            'data' => $dataDataTable
        ]);
    }

    protected function getRecordsFiltered($filters, $totalZones)
    {
        if (empty($filters['searchValue']) && !isset($filters['columnValueWithoutLike'])) {
            return $totalZones;
        } else {
            $filters['withLimit'] = false;
            return $this->zonesService->getDataDataTable($filters, true);
        }
    }

    public function editAction()
    {
        $translator = $this->TranslatorPlugin();
        $id = $this->params()->fromRoute('id', 0);
        $zone = $this->zonesService->getZoneById($id);

        if (is_null($zone)) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);
            return false;
        }
        /** @var ZoneForm $form */
        $form = $this->zoneForm;

        $zoneData = $this->hydrator->extract($zone);
        $data = [];
        $data['zone'] = $zoneData;

        $form->setData($data);
        $request = $this->getRequest();

        if ($request->isPost()) {

            // Merge post data with post file.
            $postData = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            // Set correct object (record) Id
            $postData['zone']['id'] = $zone->getId();

            // Check if the Area is KML file or GeoJSON string:
            if ($postData['zone']['useKmlFile']) {
                $areaUseGeometry = $this->postGisService->getGeometryFromGeomKMLFile($postData['zone']['kmlUpload']['tmp_name']);
            } else {
                $areaUseGeometry = $this->postGisService->getGeometryFromGeoJson($postData['zone']['areaUse']);
            }
            $postData['zone']['areaUse'] = $areaUseGeometry;

            // Set the data
            $form->setData($postData);

            if ($form->isValid()) {
                try {
                    $this->zonesService->updateZone($form->getData());
                    $this->flashMessenger()->addSuccessMessage($translator->translate('Zona modificata con successo!'));
                } catch (\Exception $e) {
                    $this->flashMessenger()
                        ->addErrorMessage($translator->translate('Si è verificato un errore applicativo.'));
                }
                return $this->redirect()->toRoute('zones');
            }
        }

        $view = new ViewModel([
            'zone' => $zone,
            'zoneForm' => $form,
        ]);
        return $view;
    }
}

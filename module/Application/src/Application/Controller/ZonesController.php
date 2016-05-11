<?php
namespace Application\Controller;

// Internals
use SharengoCore\Entity\Zone;
use Application\Form\ZoneForm;
use SharengoCore\Service\ZonesService;
use SharengoCore\Service\PostGisService;
// Externals
use Zend\Form\Form;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Stdlib\Hydrator\HydratorInterface;

class ZonesController extends AbstractActionController
{
    /**
     * @var zonesService
     */
    private $zonesService;

    /**
     * @var PostGisService
     */
    private $postGisService;
    
    /**
     * @var \Zend\Stdlib\Hydrator\HydratorInterface
     */
    private $hydrator;

    /**
     * @var ZoneForm
     */
    private $zoneForm;

    /**
     */
    public function __construct(
        ZonesService $zonesService,
        PostGisService $postGisService,
        ZoneForm $zoneForm,
        HydratorInterface $hydrator
    ) {
        $this->zonesService = $zonesService;
        $this->postGisService = $postGisService;
        $this->zoneForm = $zoneForm;
        $this->hydrator = $hydrator;
    }

    public function indexAction()
    {
        return new ViewModel();
    }

    public function listTabAction()
    {
        $view = new ViewModel([]);

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
        $as_filters = $this->params()->fromPost();
        $as_filters['withLimit'] = true;
        $as_dataDataTable = $this->zonesService->getDataDataTable($as_filters);
        $i_totalZones = $this->zonesService->getTotalZones();
        $i_recordsFiltered = $this->getRecordsFiltered($as_filters, $i_totalZones);

        return new JsonModel([
            'draw' => $this->params()->fromQuery('sEcho', 0),
            'recordsTotal' => $i_totalZones,
            'recordsFiltered' => $i_recordsFiltered,
            'data' => $as_dataDataTable
        ]);
    }

    protected function getRecordsFiltered($as_filters, $i_totalZones)
    {
        if (empty($as_filters['searchValue']) && !isset($as_filters['columnValueWithoutLike'])) {
            return $i_totalZones;
        } else {
            $as_filters['withLimit'] = false;
            return $this->zonesService->getDataDataTable($as_filters, true);
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
                $postData['zone']['areaUse'] = $areaUseGeometry;
            } else {
                $areaUseGeometry = $this->postGisService->getGeometryFromGeoJson($postData['zone']['areaUse']);
                $postData['zone']['areaUse'] = $areaUseGeometry;
            }

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
            'zone'                           => $zone,
            'zoneForm'                       => $form,
        ]);
        return $view;
    }
}

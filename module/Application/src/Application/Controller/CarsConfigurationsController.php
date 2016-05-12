<?php
namespace Application\Controller;

use  Application\Utility\CarsConfigurations\CarsConfigurationsFactory;

use Application\Form\CarsConfigurationsForm;
use SharengoCore\Entity\CarsConfigurations;
use SharengoCore\Service\CarsConfigurationsService;
use SharengoCore\Service\FleetService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Stdlib\Hydrator\HydratorInterface;

/**
 * Class ConfigurationsController
 * @package Application\Controller
 */
class CarsConfigurationsController extends AbstractActionController
{
    /**
     * @var CarsConfigurationsService
     */
    private $carsConfigurationsService;

     /**
     * @var FleetService
     */
    private $fleetService;

     /**
     * @var CarsConfigurationsForm
     */
    private $carsConfigurationsForm;

    /**
     * @var \Zend\Stdlib\Hydrator\HydratorInterface
     */
    private $hydrator;

    /**
     * CarsConfigurationsController constructor.
     *
     * @param CarsConfigurationsService $carsConfigurationsService
     */
    public function __construct(
        CarsConfigurationsService $carsConfigurationsService,
        FleetService $fleetService,
        CarsConfigurationsForm $carsConfigurationsForm,
        HydratorInterface $hydrator
    ) {
        $this->carsConfigurationsService = $carsConfigurationsService;
        $this->fleetService = $fleetService;
        $this->carsConfigurationsForm = $carsConfigurationsForm;
        $this->hydrator = $hydrator;
    }

    /**
     * @return Zend\View\Model\JsonModel
     */
    public function datatableAction()
    {
        $as_filters = $this->params()->fromPost();
        $as_filters['withLimit'] = true;
        $as_dataDataTable = $this->carsConfigurationsService->getDataDataTable($as_filters);
        $i_totalCarsConfigurations = $this->carsConfigurationsService->getTotalCarsConfigurations();
        $i_recordsFiltered = $this->_getRecordsFiltered($as_filters, $i_totalCarsConfigurations);

        foreach($as_dataDataTable as &$row)
        {
            $configurationClass = CarsConfigurationsFactory::create($row['e']['key'], $row['e']['value']);
            $row['e']['value'] = $configurationClass->getOverview();
        } 

        return new JsonModel([
            'draw' => $this->params()->fromQuery('sEcho', 0),
            'recordsTotal' => $i_totalCarsConfigurations,
            'recordsFiltered' => $i_recordsFiltered,
            'data' => $as_dataDataTable
        ]);
    }

    public function listAllAction()
    {
        return new ViewModel([]);
    }

    public function listGlobalAction()
    {
        return new ViewModel([]);
    }

    public function listFleetAction()
    {
        return new ViewModel([]);
    }

    public function listModelAction()
    {
        return new ViewModel([]);
    }

    public function listCarAction()
    {
        return new ViewModel([]);
    }

    public function detailsAction()
    {
        $id = $this->params()->fromRoute('id', 0);

        $carConfiguration = $this->carsConfigurationsService->getCarConfigurationById($id);
        $key = $carConfiguration->getKey();
        $value = $carConfiguration->getValue();

        $configurationClass = CarsConfigurationsFactory::create($key, $value);

        $hasMultipleValues = $configurationClass->hasMultipleValues();

        return new ViewModel([
            'configuration' => $carConfiguration,
            'hasMultipleValues' => $hasMultipleValues,
            'indexedValues' => $configurationClass->getIndexedValues(),
            'formattedValue' => $configurationClass->getOverview(),
            'thisId' => $id,
        ]);
    }

    public function addAction()
    {
        $translator = $this->TranslatorPlugin();
        $form = $this->carsConfigurationsForm;
        $form->setFleets($this->fleetService->getAllFleets());

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();
            $form->setData($postData);

            if ($form->isValid()) {
                // The Car Configuration form is valid, now we need to initialize the 
                // value of the new configuration.
                // This can be done by the configuration Class.

                /** @var SharengoCore\Entity\CarsConfigurations */
                $carConfigurationFromForm = $form->getData();

                // Get the CarsConfiguration key.
                $newCarConfigurationKey = $carConfigurationFromForm->getKey();

                // Get the right class.
                $configurationClass = CarsConfigurationsFactory::create($newCarConfigurationKey, '');

                // Set the default value for the specific CarConfiguration Class type.
                $defaultCarConfigurationValue = $configurationClass->getDefaultValue();

                try {
                    // Finally save the new CarConfiguration to the DB.
                    $newCarConfiguration = $this->carsConfigurationsService->save($carConfigurationFromForm, $defaultCarConfigurationValue);

                    $this->flashMessenger()->addSuccessMessage($translator->translate('Configurazione Auto aggiunta con successo!'));

                    return $this->redirect()->toRoute('cars-configurations/edit', ['id' => $newCarConfiguration->getId()]);
                } catch (\Exception $e) {
                    $this->flashMessenger()
                        ->addErrorMessage($translator->translate('Si è verificato un errore applicativo.'));
                }
            } else {
                $this->flashMessenger()->addErrorMessage($translator->translate('Dati inseriti non validi'));
            }
        }

        return new ViewModel([
            'carsConfigurationsForm' => $form
        ]);
    }

    public function editAction () {
        $id = $this->params()->fromRoute('id', 0);
        $carConfiguration = $this->carsConfigurationsService->getCarConfigurationById($id);

        $configurationClass = CarsConfigurationsFactory::create($carConfiguration->getKey(), $carConfiguration->getValue());

        /** @var  CarsConfigurations $form */
        $form = $configurationClass->getForm();

        $hasMultipleValues = $configurationClass->hasMultipleValues();
        if ($hasMultipleValues){
            $indexedValues = $configurationClass->getIndexedValues();
        } else {
            $form->setData([$carConfiguration->getKey() => $carConfiguration->getValue()]);
        }

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();

            $form->setData($postData);

            if ($form->isValid()) {
                try {
                    $this->carsConfigurationsService->save($carConfiguration, $configurationClass->getValueFromForm($postData));
                    $this->flashMessenger()->addSuccessMessage('Configurazione modificata con successo!');
                } catch (\Exception $e) {
                    $this->flashMessenger()
                         ->addErrorMessage('Si è verificato un errore applicativo.
                        L\'assistenza tecnica è già al corrente, ci scusiamo per l\'inconveniente');
                }

                return $this->redirect()->toRoute('cars-configurations/edit', ['id' => $id]);
            }
        }

        $view = new ViewModel([
            'carConfiguration' => $carConfiguration,
            'configurationClass' => $configurationClass,
            'form' => $form,
            'thisId' => $id,
            'hasMultipleValues' => $hasMultipleValues,
            'indexedValues' => ($hasMultipleValues ? $indexedValues : []),
        ]);
        return $view;
    }

    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id', 0);

        /** @var Pois $poi */
        $poi = $this->poisService->getPoiById($id);

        if (is_null($poi)) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);
            return false;
        }

        try {
            $this->poisService->deletePoi($poi);
            $this->flashMessenger()->addSuccessMessage('POI rimosso con successo!');

        } catch (\Exception $e) {
            $this->flashMessenger()
                ->addErrorMessage('Si è verificato un errore applicativo.
                L\'assistenza tecnica è già al corrente, ci scusiamo per l\'inconveniente');
        }

        return $this->redirect()->toRoute('configurations/manage-pois');
    }

    /**
     * @return Json
     */
    public function ajaxGetOptionAction()
    {
        // Get the id of the configuration
        $id = $this->params()->fromRoute('id', 0);

        // Get the id of the option of the configuration
        $optionId = $this->params()->fromRoute('optionid', 0);

        // Get the configuration.
        $carConfiguration = $this->carsConfigurationsService->getCarConfigurationById($id);

        // Get the configuration helper class.
        $configurationClass = CarsConfigurationsFactory::create($carConfiguration->getKey(), $carConfiguration->getValue());

        // Get the indexed options of the configuration
        $options = $configurationClass->getIndexedValues();

        // Get the position of the option in the options array
        $foundOption = [];
        foreach( $options as $option )
        {
            if ($option[ 'id' ] == $optionId){
                $foundOption = $option;
            }
        }

        // So, we don't need to use a JsonModel,but simply use an Http Response
        $this->getResponse()->setContent(json_encode($foundOption));

        return $this->getResponse();
    }

    protected function _getRecordsFiltered($as_filters, $i_totalCarsConfigurations)
    {
        if (empty($as_filters['searchValue']) && !isset($as_filters['columnValueWithoutLike'])) {
            return $i_totalCarsConfigurations;
        } else {
            $as_filters['withLimit'] = false;
            return $this->carsCinfigurationsService->getDataDataTable($as_filters, true);
        }
    }

}

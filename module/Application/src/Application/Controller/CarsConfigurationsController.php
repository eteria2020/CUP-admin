<?php
namespace Application\Controller;

// Internals
use Application\Utility\CarsConfigurations\CarsConfigurationsFactory;
use Application\Form\CarsConfigurationsForm;
use SharengoCore\Entity\CarsConfigurations;
use SharengoCore\Service\CarsConfigurationsService;
use SharengoCore\Service\FleetService;
// Externals
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Mvc\I18n\Translator;

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
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * CarsConfigurationsController constructor.
     *
     * @param CarsConfigurationsService $carsConfigurationsService
     * @param FleetService $fleetService
     * @param CarsConfigurationsForm $carsConfigurationsForm
     * @param HydratorInterface $hydrator
     * @param Translator $translator
     */
    public function __construct(
        CarsConfigurationsService $carsConfigurationsService,
        FleetService $fleetService,
        CarsConfigurationsForm $carsConfigurationsForm,
        HydratorInterface $hydrator,
        Translator $translator
    ) {
        $this->carsConfigurationsService = $carsConfigurationsService;
        $this->fleetService = $fleetService;
        $this->carsConfigurationsForm = $carsConfigurationsForm;
        $this->hydrator = $hydrator;
        $this->translator = $translator;
    }

    /**
     * This action return a JSON with the jQuery DataTables formatted CarsConfigurations data.
     *
     * @return JsonModel
     */
    public function datatableAction()
    {
        $filters = $this->params()->fromPost();
        $filters['withLimit'] = true;
        $dataDataTable = $this->carsConfigurationsService->getDataDataTable($filters);
        $totalCarsConfigurations = $this->carsConfigurationsService->getTotalCarsConfigurations();
        $recordsFiltered = $this->_getRecordsFiltered($filters, $totalCarsConfigurations);

        foreach ($dataDataTable as &$row) {
            $configurationClass = CarsConfigurationsFactory::create($row['e']['key'], $row['e']['value'], $this->translator);
            $row['e']['value'] = $configurationClass->getOverview();
        }

        return new JsonModel([
            'draw' => $this->params()->fromQuery('sEcho', 0),
            'recordsTotal' => $totalCarsConfigurations,
            'recordsFiltered' => $recordsFiltered,
            'data' => $dataDataTable
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

    /**
     * This method print the a detail action for a given CarConfiguration Id number.
     *
     * @param (from route) 'id' int
     */
    public function detailsAction()
    {
        $id = $this->params()->fromRoute('id', 0);

        $carConfiguration = $this->carsConfigurationsService->getCarConfigurationById($id);
        $key = $carConfiguration->getKey();
        $value = $carConfiguration->getValue();

        $configurationClass = CarsConfigurationsFactory::create($key, $value, $this->translator);

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
                $configurationClass = CarsConfigurationsFactory::create($newCarConfigurationKey, '', $this->translator);

                // Set the default value for the specific CarConfiguration Class type.
                $defaultCarConfigurationValue = $configurationClass->getDefaultValue();

                try {
                    // Finally save the new CarConfiguration to the DB.
                    $newCarConfiguration = $this->carsConfigurationsService->save($carConfigurationFromForm, $defaultCarConfigurationValue);

                    $this->flashMessenger()->addSuccessMessage($this->translator->translate('Configurazione Auto aggiunta con successo!'));

                    return $this->redirect()->toRoute('cars-configurations/edit', ['id' => $newCarConfiguration->getId()]);
                } catch (\Exception $e) {
                    $this->flashMessenger()
                        ->addErrorMessage($this->translator->translate('Si è verificato un errore applicativo.'));
                }
            } else {
                $this->flashMessenger()->addErrorMessage($this->translator->translate('Dati inseriti non validi'));
            }
        }

        return new ViewModel([
            'carsConfigurationsForm' => $form
        ]);
    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id', 0);
        $carConfiguration = $this->carsConfigurationsService->getCarConfigurationById($id);

        $configurationClass = CarsConfigurationsFactory::create($carConfiguration->getKey(), $carConfiguration->getValue(), $this->translator);

        /** @var  CarsConfigurations $form */
        $form = $configurationClass->getForm();

        $hasMultipleValues = $configurationClass->hasMultipleValues();
        if ($hasMultipleValues) {
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
                    $this->flashMessenger()->addSuccessMessage($this->translator->translate('Configurazione modificata con successo!'));
                } catch (\Exception $e) {
                    $this->flashMessenger()
                         ->addErrorMessage($this->translator->translate('Si è verificato un errore applicativo.'));
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

        /** @var CarsConfigurations $carConfiguration */
        $carConfiguration = $this->carsConfigurationsService->getCarConfigurationById($id);

        if (is_null($carConfiguration)) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);
            return false;
        }

        try {
            $this->carsConfigurationsService->deleteCarConfiguration($carConfiguration);
            $this->flashMessenger()->addSuccessMessage($this->translator->translate('Configurazione Auto rimossa con successo!'));
        } catch (\Exception $e) {
            $this->flashMessenger()
                ->addErrorMessage($this->translator->translate('Si è verificato un errore applicativo.'));
        }

        return $this->redirect()->toRoute('cars-configurations/edit', ['id' => $id]);
    }

    public function deleteOptionAction()
    {
        // Get the id of the configuration
        $id = $this->params()->fromRoute('id', 0);

        // Get the id of the option of the configuration
        $optionId = $this->params()->fromRoute('optionid', 0);

        // Get the configuration.
        $carConfiguration = $this->carsConfigurationsService->getCarConfigurationById($id);

        // Get the configuration helper class.
        $configurationClass = CarsConfigurationsFactory::create($carConfiguration->getKey(), $carConfiguration->getValue(), $this->translator);

        // Get the indexed options of the configuration
        $options = $configurationClass->getIndexedValues();

        if (empty($options)) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);
            return false;
        }

        // Remove the sepecific option
        foreach ($options as $key => &$option) {
            if ($option['id'] === $optionId) {
                unset($options[$key]);
            }
            // Remove the index from the radio configuration
            unset($option['id']);
        }

        // Update the carConfiguration value
        $configurationClass->setValue($options);

        try {
            // Finally save the updated CarConfiguration to the DB.
            $this->carsConfigurationsService->save($carConfiguration, $configurationClass->getRawValue());
            $this->flashMessenger()->addSuccessMessage($this->translator->translate('Configurazione Auto aggioranta con successo!'));
        } catch (\Exception $e) {
            $this->flashMessenger()
                ->addErrorMessage($this->translator->translate('Si è verificato un errore applicativo.'));
        }

        return $this->redirect()->toRoute('cars-configurations/edit', ['id' => $id]);
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
        $configurationClass = CarsConfigurationsFactory::create($carConfiguration->getKey(), $carConfiguration->getValue(), $this->translator);

        // Get the indexed options of the configuration
        $options = $configurationClass->getIndexedValues();

        // Get the position of the option in the options array
        $foundOption = [];
        foreach ($options as $option) {
            if ($option[ 'id' ] == $optionId) {
                $foundOption = $option;
            }
        }

        // So, we don't need to use a JsonModel,but simply use an Http Response
        $this->getResponse()->setContent(json_encode($foundOption));

        return $this->getResponse();
    }

    protected function _getRecordsFiltered($filters, $totalCarsConfigurations)
    {
        if (empty($filters['searchValue']) && !isset($filters['columnValueWithoutLike'])) {
            return $totalCarsConfigurations;
        } else {
            $filters['withLimit'] = false;
            return $this->carsCinfigurationsService->getDataDataTable($filters, true);
        }
    }
}

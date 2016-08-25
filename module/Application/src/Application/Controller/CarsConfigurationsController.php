<?php
namespace Application\Controller;

// Internals
use Application\Utility\CarsConfigurations\CarsConfigurationsTypesFactory;
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
            $configurationTypeClass = CarsConfigurationsTypesFactory::create($row['e']['key'], $row['e']['value'], $this->translator);
            $row['e']['value'] = $configurationTypeClass->getOverview();
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
     * @return ViewModel
     */
    public function detailsAction()
    {
        try {
            // Get the configuration.
            $carConfiguration = $this->getCarConfigurationFromRouteId();
        } catch (CarConfigurationNotFoundException $e) {
            $this->flashMessenger()
                ->addErrorMessage($this->translator->translate('La Configurazione Auto non è stata trovata!'));
        }

        $configurationTypeClass = CarsConfigurationsTypesFactory::createFromCarConfiguration($carConfiguration, $this->translator);

        $hasMultipleValues = $configurationTypeClass::HAS_MULTIPLE_VALUES;

        return new ViewModel([
            'configuration' => $carConfiguration,
            'hasMultipleValues' => $hasMultipleValues,
            'indexedValues' => $hasMultipleValues ? $configurationTypeClass->getIndexedValueOptions() : $configurationTypeClass->getValue(),
            'formattedValue' => $configurationTypeClass->getOverview(),
            'thisId' => $carConfiguration->getId(),
        ]);
    }

    public function addAction()
    {
        $form = $this->carsConfigurationsForm;

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
                $configurationTypeClass = CarsConfigurationsTypesFactory::create($newCarConfigurationKey, '', $this->translator);

                // Set the default value for the specific CarConfiguration Class type.
                $defaultCarConfigurationValue = $configurationTypeClass->getDefaultValue();

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
        try {
            // Get the configuration.
            $carConfiguration = $this->getCarConfigurationFromRouteId();
        } catch (CarConfigurationNotFoundException $e) {
            $this->flashMessenger()
                ->addErrorMessage($this->translator->translate('La Configurazione Auto non è stata trovata!'));
            return new ViewModel([]);
        }
        $id = $carConfiguration->getId();

        $configurationTypeClass = CarsConfigurationsTypesFactory::createFromCarConfiguration($carConfiguration, $this->translator);

        /** @var  CarsConfigurations $form */
        $form = $configurationTypeClass->getForm();

        $hasMultipleValues = $configurationTypeClass::HAS_MULTIPLE_VALUES;

        if ($hasMultipleValues) {
            $indexedValues = $configurationTypeClass->getIndexedValueOptions();
        } else {
            $indexedValues = [];
            $form->setData([
                'value' => $carConfiguration->getValue()
            ]);
        }

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();

            $form->setData($postData);

            if ($form->isValid()) {
                try {
                    $configurationTypeClass->updateValue($postData);
                    $this->carsConfigurationsService->save($carConfiguration, $configurationTypeClass->getRawValue());
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
            'configurationTypeClass' => $configurationTypeClass,
            'form' => $form,
            'thisId' => $id,
            'hasMultipleValues' => $hasMultipleValues,
            'indexedValues' => $indexedValues,
        ]);
        return $view;
    }

    public function deleteAction()
    {
        try {
            // Get the configuration.
            $carConfiguration = $this->getCarConfigurationFromRouteId();
        } catch (CarConfigurationNotFoundException $e) {
            $this->flashMessenger()
                ->addErrorMessage($this->translator->translate('La Configurazione Auto non è stata trovata!'));
        }

        if (!$carConfiguration instanceof CarsConfigurations) {
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

        return $this->redirect()->toRoute('cars-configurations/edit', ['id' => $carConfiguration->getId()]);
    }

    public function deleteOptionAction()
    {
        try {
            // Get the configuration.
            $carConfiguration = $this->getCarConfigurationFromRouteId();
        } catch (CarConfigurationNotFoundException $e) {
            $this->flashMessenger()
                ->addErrorMessage($this->translator->translate('La Configurazione Auto non è stata trovata!'));
        }

        // Get the id of the option of the configuration
        $optionId = $this->params()->fromRoute('optionid', 0);

        // Get the configuration helper class.
        $configurationTypeClass = CarsConfigurationsTypesFactory::createFromCarConfiguration($carConfiguration, $this->translator);

        // Update the carConfiguration value
        $configurationTypeClass->deleteValueOption($optionId);

        try {
            // Finally save the updated CarConfiguration to the DB.
            $this->carsConfigurationsService->save($carConfiguration, $configurationTypeClass->getRawValue());
            $this->flashMessenger()->addSuccessMessage($this->translator->translate('Configurazione Auto aggioranta con successo!'));
        } catch (\Exception $e) {
            $this->flashMessenger()
                ->addErrorMessage($this->translator->translate('Si è verificato un errore applicativo.'));
        }

        return $this->redirect()->toRoute('cars-configurations/edit', ['id' => $carConfiguration->getId()]);
    }

    /**
     * This method return a JSON containing the configuration value
     * of a specific option, from a given option "id" key.
     *
     * @return JsonModel
     */
    public function ajaxGetOptionAction()
    {
        try {
            // Get the configuration.
            $carConfiguration = $this->getCarConfigurationFromRouteId();
        } catch (CarConfigurationNotFoundException $e) {
            $this->flashMessenger()
                ->addErrorMessage($this->translator->translate('La Configurazione Auto non è stata trovata!'));
        }

        // Get the id of the option of the configuration
        $optionId = $this->params()->fromRoute('optionid', 0);

        // Get the configuration helper class.
        $configurationTypeClass = CarsConfigurationsTypesFactory::createFromCarConfiguration($carConfiguration, $this->translator);

        // Get the indexed options of the configuration
        $options = $configurationTypeClass->getIndexedValueOptions();

        // Get the position of the option in the options array
        $foundOption = [];
        foreach ($options as $option) {
            if ($option[ 'id' ] == $optionId) {
                $foundOption = $option;
            }
        }

        return new JsonModel($foundOption);
    }

    protected function _getRecordsFiltered($filters, $totalCarsConfigurations)
    {
        if (empty($filters['searchValue']) && !isset($filters['columnValueWithoutLike'])) {
            return $totalCarsConfigurations;
        } else {
            $filters['withLimit'] = false;
            return $this->carsConfigurationsService->getDataDataTable($filters, true);
        }
    }

    /**
     * Get the CarConfiguration from the route param "id".
     *
     * @throws CarConfigurationNotFoundException
     * @return CarsConfigurations
     */
    protected function getCarConfigurationFromRouteId()
    {
        // Get the id from route
        $id = $this->params()->fromRoute('id', 0);

        /** @var CarsConfigurations $carConfiguration */
        $carConfiguration = $this->carsConfigurationsService->getCarConfigurationById($id);

        if (!$carConfiguration instanceof CarsConfigurations) {
            throw new CarConfigurationNotFoundException();
        }

        return $carConfiguration;
    }
}

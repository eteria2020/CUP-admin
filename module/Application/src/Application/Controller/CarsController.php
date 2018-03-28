<?php

namespace Application\Controller;

// Internals
use Application\Controller\Plugin\TranslatorPlugin;
use Application\Form\CarForm;
use Application\Listener\LanguageFromSessionDetectorListener;
use Application\Service\UserLanguageService;
use MvLabsMultilanguage\Service\LanguageService;
use SharengoCore\Entity\Cars;
use SharengoCore\Entity\CarsMaintenance;
use SharengoCore\Entity\Commands;
use SharengoCore\Service\CarsService;
use SharengoCore\Service\CarsDamagesService;
use SharengoCore\Service\CommandsService;
use SharengoCore\Utility\CarStatus;
// Externals
use Zend\Form\Form;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

class CarsController extends AbstractActionController {

    /**
     * @var CarsService
     */
    private $carsService;

    /**
     * @var CommandsService
     */
    private $commandsService;

    /**
     * @var Form
     */
    private $carForm;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var Container
     */
    private $datatableFiltersSessionContainer;

    /**
     * @param CarsService $carsService
     * @param CommandsService $commandsService
     * @param Form $carForm
     * @param HydratorInterface $hydrator
     * @param Container $datatableFiltersSessionContainer
     */
    public function __construct(
    CarsService $carsService, CommandsService $commandsService, Form $carForm, HydratorInterface $hydrator, Container $datatableFiltersSessionContainer
    ) {
        $this->carsService = $carsService;
        $this->commandsService = $commandsService;
        $this->carForm = $carForm;
        $this->hydrator = $hydrator;
        $this->datatableFiltersSessionContainer = $datatableFiltersSessionContainer;
    }

    /**
     * This method return an array containing the DataTable filters,
     * from a Session Container.
     *
     * @return array
     */
    private function getDataTableSessionFilters() {
        return $this->datatableFiltersSessionContainer->offsetGet('Cars');
    }

    public function indexAction() {
        $sessionDatatableFilters = $this->getDataTableSessionFilters();

        return new ViewModel([
            'filters' => json_encode($sessionDatatableFilters),
        ]);
    }

    public function datatableAction() {
        $as_filters = $this->params()->fromPost();
        $as_filters['withLimit'] = true;
        $as_dataDataTable = $this->carsService->getDataDataTable($as_filters);
        $i_totalCars = $this->carsService->getTotalCars();
        $i_recordsFiltered = $this->_getRecordsFiltered($as_filters, $i_totalCars);

        return new JsonModel([
            'draw' => $this->params()->fromQuery('sEcho', 0),
            'recordsTotal' => $i_totalCars,
            'recordsFiltered' => $i_recordsFiltered,
            'data' => $as_dataDataTable
        ]);
    }

    public function addAction() {
        $translator = $this->TranslatorPlugin();
        $form = $this->carForm;
        $form->setStatus([CarStatus::OPERATIVE => CarStatus::OPERATIVE]);
        $form->setFleets($this->carsService->getFleets());

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();
            $form->setData($postData);
            $form->getInputFilter()->get('location')->setRequired(false);

            if ($form->isValid()) {

                try {

                    $this->carsService->saveData($form->getData());
                    $this->flashMessenger()->addSuccessMessage($translator->translate('Auto aggiunta con successo!'));
                } catch (\Exception $e) {

                    $this->flashMessenger()->addErrorMessage($translator->translate('Si è verificato un errore applicativo. L\'assistenza tecnica è già al corrente, ci scusiamo per l\'inconveniente'));
                }

                return $this->redirect()->toRoute('cars');
            }
        }

        return new ViewModel([
            'carForm' => $form
        ]);
    }

    public function editAction() {
        $plate = $this->params()->fromRoute('plate', 0);
        $car = $this->carsService->getCarByPlate($plate);
        $tab = $this->params()->fromQuery('tab', 'edit');

        return new ViewModel([
            'car' => $car,
            'tab' => $tab
        ]);
    }

    public function editTabAction() {
        $translator = $this->TranslatorPlugin();
        $plate = $this->params()->fromRoute('plate', 0);
        $car = $this->carsService->getCarByPlate($plate);
        $disableInputStatusMaintenance = false;

        if (is_null($car)) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);
            return false;
        }

        /** @var CarsMaintenance $lastUpdateCar */
        $lastCarsMaintenance = $this->carsService->getLastCarsMaintenance($car->getPlate());

        /** @var CarForm $form */
        $form = $this->carForm;
        $form->setStatus($this->carsService->getStatusCarAvailable($car->getStatus()));
        $form->setFleets($this->carsService->getFleets());
        $carData = $this->hydrator->extract($car);
        $data = [];
        $data['car'] = $carData;

        if (!is_null($lastCarsMaintenance) && $car->getStatus() == CarStatus::MAINTENANCE) {
            $data['location'] = $lastCarsMaintenance->getLocation();
            $data['note'] = $lastCarsMaintenance->getNotes();
            $data['motivation'] = $lastCarsMaintenance->getMotivation()->getId();
            $disableInputStatusMaintenance = true;
        }

        $form->setData($data);
        $lastStatus = $car->getStatus();

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();
            $postData['car']['plate'] = $car->getPlate();
            $form->setData($postData);
            //$form->get('car')->remove('fleet'); // setValue($car->getFleet()->getId());
            $form->getInputFilter()->get('location')->setRequired(false);
            $form->getInputFilter()->get('motivation')->setRequired(false);

            if ($form->isValid()) {
                try {
                    $this->carsService->updateCar($form->getData(), $lastStatus, $postData, $this->identity());
                    $this->carsService->saveData($form->getData(), false);
                    $this->flashMessenger()->addSuccessMessage($translator->translate('Auto modificata con successo!'));
                } catch (\Exception $e) {

                    $this->flashMessenger()->addErrorMessage($translator->translate('Si è verificato un errore applicativo. L\'assistenza tecnica è già al corrente, ci scusiamo per l\'inconveniente'));
                }

                $url = $this->url()->fromRoute('cars/edit', ['plate' => $car->getPlate()]) . '#edit';
                return $this->redirect()->toUrl($url);
            }
        }

        $view = new ViewModel([
            'car' => $car,
            'carForm' => $form,
            'disableInputStatusMaintenance' => $disableInputStatusMaintenance
        ]);
        $view->setTerminal(true);
        return $view;
    }

    public function commandsTabAction() {
        $plate = $this->params()->fromRoute('plate', 0);
        $car = $this->carsService->getCarByPlate($plate);
        $commands = Commands::getCommandCodes();
        unset($commands[Commands::CLOSE_TRIP]);
        $view = new ViewModel([
            'commands' => $commands,
            'car' => $car,
        ]);
        $view->setTerminal(true);
        return $view;
    }

    public function damagesTabAction() {
        $translator = $this->TranslatorPlugin();
        $plate = $this->params()->fromRoute('plate', 0);
        $car = $this->carsService->getCarByPlate($plate);
        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();
            try {
                if (isset($postData['damages'])) {
                    $this->carsService->updateDamages($car, $postData['damages']);
                } else {
                    $this->carsService->updateDamages($car, null);
                }
                $this->flashMessenger()->addSuccessMessage($translator->translate('Danni auto modificati con successo!'));
            } catch (\Exception $e) {
                $this->flashMessenger()->addErrorMessage($translator->translate('Si è verificato un errore applicativo. L\'assistenza tecnica è già al corrente, ci scusiamo per l\'inconveniente'));
            }

            $url = $this->url()->fromRoute('cars/edit', ['plate' => $car->getPlate()]) . '#damages';
            return $this->redirect()->toUrl($url);
        }

        $view = new ViewModel([
            'damages' => $this->carsService->getDamagesList(),
            'car' => $car,
            'carDamages' => json_decode($car->getDamages(), true)
        ]);
        $view->setTerminal(true);
        return $view;
    }

    public function deleteAction() {
        $translator = $this->TranslatorPlugin();
        $plate = $this->params()->fromRoute('plate', 0);

        /** @var Cars $car */
        $car = $this->carsService->getCarByPlate($plate);

        if (is_null($car)) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);

            return false;
        }

        try {

            $this->carsService->deleteCar($car);
            $this->flashMessenger()->addSuccessMessage($translator->translate('Auto rimossa con successo!'));
        } catch (\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException $e) {

            $this->flashMessenger()->addErrorMessage($translator->translate('L\'auto non può essere rimossa perchè ha effettuato una o più corse.'));
        }

        return $this->redirect()->toRoute('cars');
    }

    public function sendCommandAction() {
        error_log("sendCommandAction log");
        $translator = $this->TranslatorPlugin();
        $plate = $this->params()->fromRoute('plate', 0);
        $commandIndex = $this->params()->fromRoute('command', 0);
        
        $intArg1 = $this->params()->fromRoute('intArg1', 0) != null ? $this->params()->fromRoute('intArg1', 0) : null;
        $intArg2 = $this->params()->fromRoute('intArg2', 0) != null ? $this->params()->fromRoute('intArg2', 0) : null;
        $txtArg1 = $this->params()->fromRoute('txtArg1', 0) != null ? $this->params()->fromRoute('txtArg1', 0) : null;
        $txtArg2 = $this->params()->fromRoute('txtArg2', 0) != null ? $this->params()->fromRoute('txtArg2', 0) : null;
        $ttl = $this->params()->fromRoute('ttl', 0) != null ? $this->params()->fromRoute('ttl', 0) : null;
        
        $car = $this->carsService->getCarByPlate($plate);

        if (is_null($car)) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);
            return false;
        }

        try {
            $this->commandsService->sendCommand($car, $commandIndex, $this->identity(), $intArg1, $intArg2,  $txtArg1, $txtArg2, $ttl);
            $this->flashMessenger()->addSuccessMessage($translator->translate('Comando eseguito con successo'));
        } catch (\Exception $e) {
            $this->flashMessenger()->addErrorMessage($translator->translate('Errore nell\'esecuzione del comando'));
        }

        $url = $this->url()->fromRoute('cars/edit', ['plate' => $car->getPlate()]) . '#commands';
        return $this->redirect()->toUrl($url);
    }

    protected function _getRecordsFiltered($as_filters, $i_totalCars) {
        if (empty($as_filters['searchValue']) && !isset($as_filters['columnValueWithoutLike'])) {

            return $i_totalCars;
        } else {

            $as_filters['withLimit'] = false;

            return $this->carsService->getDataDataTable($as_filters, true);
        }
    }

}

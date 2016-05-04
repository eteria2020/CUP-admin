<?php
namespace Application\Controller;

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
use Zend\Form\Form;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;
use Zend\Session\Container;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class CarsController extends AbstractActionController
{
    /**
     * @var CarsService
     */
    private $carsService;

    /** @var  CommandsService */
    private $commandsService;

    /**
     * @var
     */
    private $carForm;

    /**
     * @var \Zend\Stdlib\Hydrator\HydratorInterface
     */
    private $hydrator;

    public function __construct(
        CarsService $carsService,
        CommandsService $commandsService,
        Form $carForm,
        HydratorInterface $hydrator
    )
    {
        $this->carsService = $carsService;
        $this->commandsService = $commandsService;
        $this->carForm = $carForm;
        $this->hydrator = $hydrator;
    }

    public function indexAction()
    {
        $sessionDatatableFilters = $this->carsService->getDataTableSessionFilters();

        return new ViewModel([
            'filters' => json_encode($sessionDatatableFilters),
        ]);
    }

    public function datatableAction()
    {
        $as_filters = $this->params()->fromPost();
        $as_filters['withLimit'] = true;
        $as_dataDataTable = $this->carsService->getDataDataTable($as_filters);
        $i_totalCars = $this->carsService->getTotalCars();
        $i_recordsFiltered = $this->_getRecordsFiltered($as_filters, $i_totalCars);

        return new JsonModel([
            'draw'            => $this->params()->fromQuery('sEcho', 0),
            'recordsTotal'    => $i_totalCars,
            'recordsFiltered' => $i_recordsFiltered,
            'data'            => $as_dataDataTable
        ]);
    }

    public function addAction()
    {
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

    public function editAction () {
        $plate = $this->params()->fromRoute('plate', 0);
        $car = $this->carsService->getCarByPlate($plate);
        $tab = $this->params()->fromQuery('tab', 'edit');

        return new ViewModel([
            'car'       => $car,
            'tab'       => $tab
        ]);
    }

    public function editTabAction()
    {
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
        $carData = $this->hydrator->extract($car);
        $data = [];
        $data['car'] = $carData;

        if(!is_null($lastCarsMaintenance) && $car->getStatus() == CarStatus::MAINTENANCE) {
            $data['location'] = $lastCarsMaintenance->getLocation();
            $data['note'] = $lastCarsMaintenance->getNotes();
            $disableInputStatusMaintenance = true;
        }

        $form->setData($data);
        $lastStatus = $car->getStatus();

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();
            $postData['car']['plate'] = $car->getPlate();
            $form->setData($postData);
            $form->get('car')->remove('fleet'); // setValue($car->getFleet()->getId());
            $form->getInputFilter()->get('location')->setRequired(false);

            if ($form->isValid()) {

                try {

                    $this->carsService->updateCar($form->getData(), $lastStatus, $postData);
                    $this->carsService->saveData($form->getData(), false);
                    $this->flashMessenger()->addSuccessMessage($translator->translate('Auto modificata con successo!'));

                } catch (\Exception $e) {

                    $this->flashMessenger()->addErrorMessage($translator->translate('Si è verificato un errore applicativo. L\'assistenza tecnica è già al corrente, ci scusiamo per l\'inconveniente'));

                }

                $url = $this->url()->fromRoute('cars/edit', ['plate' => $car->getPlate()]).'#edit';
                return $this->redirect()->toUrl($url);
            }
        }

        $view = new ViewModel([
            'car'                           => $car,
            'carForm'                       => $form,
            'disableInputStatusMaintenance' => $disableInputStatusMaintenance
        ]);
        $view->setTerminal(true);
        return $view;
    }

    public function commandsTabAction () {
        $plate = $this->params()->fromRoute('plate', 0);
        $car = $this->carsService->getCarByPlate($plate);
        $commands = Commands::getCommandCodes();
        unset($commands[Commands::CLOSE_TRIP]);
        $view = new ViewModel([
            'commands' => $commands,
            'car'      => $car,
        ]);
        $view->setTerminal(true);
        return $view;
    }

    public function damagesTabAction () {
        $translator = $this->TranslatorPlugin();
        $plate = $this->params()->fromRoute('plate', 0);
        $car = $this->carsService->getCarByPlate($plate);
        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();
            try {
                $this->carsService->updateDamages($car, $postData['damages']);
                $this->flashMessenger()->addSuccessMessage($translator->translate('Danni auto modificati con successo!'));
            } catch (\Exception $e) {
                $this->flashMessenger()->addErrorMessage($translator->translate('Si è verificato un errore applicativo. L\'assistenza tecnica è già al corrente, ci scusiamo per l\'inconveniente'));
            }

            $url = $this->url()->fromRoute('cars/edit', ['plate' => $car->getPlate()]).'#damages';
            return $this->redirect()->toUrl($url);
        }

        $view = new ViewModel([
            'damages'        => $this->carsService->getDamagesList(),
            'car'            => $car,
            'carDamages'     => json_decode($car->getDamages(), true)
        ]);
        $view->setTerminal(true);
        return $view;
    }

    public function deleteAction()
    {
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

    public function sendCommandAction()
    {
        $translator = $this->TranslatorPlugin();
        $plate = $this->params()->fromRoute('plate', 0);
        $commandIndex = $this->params()->fromRoute('command', 0);
        $car = $this->carsService->getCarByPlate($plate);

        if (is_null($car)) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);

            return false;
        }

        try {

            $this->commandsService->sendCommand($car, $commandIndex, $this->identity());
            $this->flashMessenger()->addSuccessMessage($translator->translate('Comando eseguito con successo'));

        } catch (\Exception $e) {

            $this->flashMessenger()->addErrorMessage($translator->translate('Errore nell\'esecuzione del comando'));

        }

        $url = $this->url()->fromRoute('cars/edit', ['plate' => $car->getPlate()]).'#commands';
        return $this->redirect()->toUrl($url);
    }

    protected function _getRecordsFiltered($as_filters, $i_totalCars)
    {
        if (empty($as_filters['searchValue']) && !isset($as_filters['columnValueWithoutLike'])) {

            return $i_totalCars;

        } else {

            $as_filters['withLimit'] = false;

            return $this->carsService->getDataDataTable($as_filters, true);
        }
    }
}

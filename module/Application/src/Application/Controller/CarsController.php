<?php
namespace Application\Controller;

use Application\Form\CarForm;
use SharengoCore\Entity\Cars;
use SharengoCore\Entity\CarsMaintenance;
use SharengoCore\Entity\Commands;
use SharengoCore\Service\CarsService;
use SharengoCore\Service\CommandsService;
use SharengoCore\Utility\CarStatus;
use Zend\Form\Form;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class CarsController extends AbstractActionController
{
    /**
     * @var CarsService
     */
    private $I_carsService;

    /** @var  CommandsService */
    private $I_commandsService;

    /**
     * @var
     */
    private $I_carForm;

    /**
     * @var \Zend\Stdlib\Hydrator\HydratorInterface
     */
    private $hydrator;

    public function __construct(
        CarsService $I_carsService,
        CommandsService $I_commandsService,
        Form $I_carForm,
        HydratorInterface $hydrator)
    {
        $this->I_carsService = $I_carsService;
        $this->I_commandsService = $I_commandsService;
        $this->I_carForm = $I_carForm;
        $this->hydrator = $hydrator;
    }

    public function indexAction()
    {
        return new ViewModel([]);
    }

    public function datatableAction()
    {
        $as_filters = $this->params()->fromPost();
        $as_filters['withLimit'] = true;
        $as_dataDataTable = $this->I_carsService->getDataDataTable($as_filters);
        $i_totalCars = $this->I_carsService->getTotalCars();
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
        $form = $this->I_carForm;
        $form->setStatus([CarStatus::OPERATIVE => CarStatus::OPERATIVE]);

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();
            $form->setData($postData);
            $form->getInputFilter()->get('location')->setRequired(false);

            if ($form->isValid()) {

                try {

                    $this->I_carsService->saveData($form->getData());
                    $this->flashMessenger()->addSuccessMessage('Auto aggiunta con successo!');

                } catch (\Exception $e) {

                    $this->flashMessenger()->addErrorMessage($e->getMessage());

                }

                return $this->redirect()->toRoute('cars');
            }
        }

        return new ViewModel([
            'carForm' => $form
        ]);
    }

    public function editAction()
    {
        $plate = $this->params()->fromRoute('plate', 0);
        $I_car = $this->I_carsService->getCarByPlate($plate);
        $disableInputStatusMaintenance = false;

        if (is_null($I_car)) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);

            return false;
        }

        /** @var CarsMaintenance $lastUpdateCar */
        $lastCarsMaintenance = $this->I_carsService->getLastCarsMaintenance($I_car->getPlate());

        /** @var CarForm $form */
        $form = $this->I_carForm;
        $form->setStatus($this->I_carsService->getStatusCarAvailable($I_car->getStatus()));
        $carData = $this->hydrator->extract($I_car);
        $data = [];
        $data['car'] = $carData;

        if(!is_null($lastCarsMaintenance) && $I_car->getStatus() == CarStatus::MAINTENANCE) {
            $data['location'] = $lastCarsMaintenance->getLocation();
            $data['note'] = $lastCarsMaintenance->getNotes();
            $disableInputStatusMaintenance = true;
        }

        $form->setData($data);
        $lastStatus = $I_car->getStatus();

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();
            $postData['car']['plate'] = $I_car->getPlate();
            $form->setData($postData);
            $form->getInputFilter()->get('location')->setRequired(false);

            if ($form->isValid()) {

                try {

                    $this->I_carsService->updateCar($form->getData(), $lastStatus, $postData);
                    $this->I_carsService->saveData($form->getData(), false);
                    $this->flashMessenger()->addSuccessMessage('Auto modificata con successo!');

                } catch (\Exception $e) {

                    $this->flashMessenger()->addErrorMessage($e->getMessage());

                }

                return $this->redirect()->toRoute('cars');
            }
        }

        return new ViewModel([
            'car'                           => $I_car,
            'carForm'                       => $form,
            'commands'                      => Commands::getCommandCodes(),
            'disableInputStatusMaintenance' => $disableInputStatusMaintenance
        ]);
    }

    public function deleteAction()
    {
        $plate = $this->params()->fromRoute('plate', 0);

        /** @var Cars $I_car */
        $I_car = $this->I_carsService->getCarByPlate($plate);

        if (is_null($I_car)) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);

            return false;
        }

        try {

            $this->I_carsService->deleteCar($I_car);
            $this->flashMessenger()->addSuccessMessage('Auto rimossa con successo!');

        } catch (\Exception $e) {

            $this->flashMessenger()->addErrorMessage($e->getMessage());

        }

        return $this->redirect()->toRoute('cars');

    }

    public function sendCommandAction()
    {
        $plate = $this->params()->fromRoute('plate', 0);
        $commandIndex = $this->params()->fromRoute('command', 0);
        $I_car = $this->I_carsService->getCarByPlate($plate);

        if (is_null($I_car)) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);

            return false;
        }

        try {

            $this->I_commandsService->sendCommand($I_car, $commandIndex);
            $this->flashMessenger()->addSuccessMessage('Comando lanciato con successo');

        } catch (\Exception $e) {

            $this->flashMessenger()->addErrorMessage('Errore nel lanciare il comando');

        }

        return $this->redirect()->toRoute('cars/edit', ['plate' => $I_car->getPlate()]);
    }

    protected function _getRecordsFiltered($as_filters, $i_totalCars)
    {
        if (empty($as_filters['searchValue']) && !isset($as_filters['columnValueWithoutLike'])) {

            return $i_totalCars;

        } else {

            $as_filters['withLimit'] = false;

            return count($this->I_carsService->getDataDataTable($as_filters));
        }
    }
}
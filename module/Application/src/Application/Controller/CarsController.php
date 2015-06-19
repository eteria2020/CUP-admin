<?php
namespace Application\Controller;

use SharengoCore\Entity\Cars;
use SharengoCore\Service\CarsService;
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
    public $I_carsService;

    /**
     * @var
     */
    private $I_carForm;

    /**
     * @var \Zend\Stdlib\Hydrator\HydratorInterface
     */
    private $hydrator;

    public function __construct(CarsService $I_carsService, Form $I_carForm, HydratorInterface $hydrator)
    {
        $this->I_carsService = $I_carsService;
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

        return new JsonModel(array(
            'draw'            => $this->params()->fromQuery('sEcho', 0),
            'recordsTotal'    => $i_totalCars,
            'recordsFiltered' => $i_recordsFiltered,
            'data'            => $as_dataDataTable
        ));
    }

    public function addAction()
    {
        $form = $this->I_carForm;

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();
            $form->setData($postData);

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

        if (is_null($I_car)) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);

            return false;
        }

        $form = $this->I_carForm;
        $carData = $this->hydrator->extract($I_car);
        $form->setData(['car' => $carData]);

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();
            $postData['car']['plate'] = $I_car->getPlate();

            $form->setData($postData);

            if ($form->isValid()) {

                try {

                    $this->I_carsService->saveData($form->getData(), false);
                    $this->flashMessenger()->addSuccessMessage('Auto modificata con successo!');

                } catch (\Exception $e) {

                    $this->flashMessenger()->addErrorMessage($e->getMessage());

                }

                return $this->redirect()->toRoute('cars');
            }
        }

        return new ViewModel([
            'car'     => $I_car,
            'carForm' => $form
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
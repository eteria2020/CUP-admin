<?php

namespace Application\Controller;

use Application\Form\PoiForm;
use SharengoCore\Entity\Pois;
use SharengoCore\Service\CarsService;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use SharengoCore\Service\PoisService;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class PoisController extends AbstractActionController
{
    /**
     * @var PoisService
     */
    private $poisService;

    /**
     * @var CarsService
     */
    private $carsService;

    /**
     * @var \Zend\Stdlib\Hydrator\HydratorInterface
     */
    private $hydrator;

    /**
     * @var PoiForm
     */
    private $poiForm;

    public function __construct(
        PoisService $poisService,
        CarsService $carsService,
        PoiForm $poiForm,
        HydratorInterface $hydrator
    ) {
        $this->poisService = $poisService;
        $this->carsService = $carsService;
        $this->poiForm = $poiForm;
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
        $as_dataDataTable = $this->poisService->getDataDataTable($as_filters);
        $i_totalPois = $this->poisService->getTotalPois();
        $i_recordsFiltered = $this->getRecordsFiltered($as_filters, $i_totalPois);

        return new JsonModel([
            'draw'            => $this->params()->fromQuery('sEcho', 0),
            'recordsTotal'    => $i_totalPois,
            'recordsFiltered' => $i_recordsFiltered,
            'data'            => $as_dataDataTable
        ]);
    }

    public function addAction()
    {
        $form = $this->poiForm;
        $form->setFleets($this->carsService->getFleets());

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();
            $form->setData($postData);

            if ($form->isValid()) {
                try {
                    $this->poisService->saveData($form->getData());
                    $this->flashMessenger()->addSuccessMessage('POI aggiunta con successo!');

                } catch (\Exception $e) {
                    $this->flashMessenger()
                        ->addErrorMessage('Si è verificato un errore applicativo.
                        L\'assistenza tecnica è già al corrente, ci scusiamo per l\'inconveniente');

                }

                return $this->redirect()->toRoute('configurations/manage-pois');
            }
        }

        return new ViewModel([
            'poiForm' => $form
        ]);
    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id', 0);
        $poi = $this->poisService->getPoiById($id);

        if (is_null($poi)) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);
            return false;
        }

        /** @var PoiForm $form */
        $form = $this->poiForm;
        $form->setFleets($this->carsService->getFleets());

        $poiData = $this->hydrator->extract($poi);
        $data = [];
        $data['poi'] = $poiData;

        $form->setData($data);

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();
            $postData['poi']['id'] = $poi->getId();

            $form->setData($postData);
            $form->get('poi')->remove('fleet');

            if ($form->isValid()) {
                try {
                    $this->poisService->saveData($form->getData(),true);
                    $this->flashMessenger()->addSuccessMessage('POI modificato con successo!');

                } catch (\Exception $e) {
                    $this->flashMessenger()
                        ->addErrorMessage('Si è verificato un errore applicativo.
                        L\'assistenza tecnica è già al corrente, ci scusiamo per l\'inconveniente');
                }

                return $this->redirect()->toRoute('configurations/manage-pois');
            }
        }

        $view = new ViewModel([
            'poi'                           => $poi,
            'poiForm'                       => $form,
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

    protected function getRecordsFiltered($as_filters, $i_totalPois)
    {
        if (empty($as_filters['searchValue']) && !isset($as_filters['columnValueWithoutLike'])) {
            return $i_totalPois;
        } else {
            $as_filters['withLimit'] = false;
            return count($this->poisService->getDataDataTable($as_filters));
        }
    }
}

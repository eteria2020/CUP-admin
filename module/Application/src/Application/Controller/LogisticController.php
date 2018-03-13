<?php

namespace Application\Controller;

use SharengoCore\Service\CarsService;
use SharengoCore\Service\WebusersService;
use SharengoCore\Service\MaintenanceMotivationsService;

use Zend\EventManager\EventManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

class LogisticController extends AbstractActionController {

    /**
     * @var CarsService
     */
    private $carsService;
    
    /**
     * @var WebusersService
     */
    private $webusersService;
    
    /**
     * @var MaintenanceMotivationsService
     */
    private $maintenanceMotivationsService;

    /**
     * @param CarsService $carsService
     * @param WebusersService $webusersService
     */
    public function __construct(
    CarsService $carsService, WebusersService $webusersService, MaintenanceMotivationsService $maintenanceMotivationsService
    ) {
        $this->carsService = $carsService;
        $this->webusersService = $webusersService;
        $this->maintenanceMotivationsService = $maintenanceMotivationsService;
    }
    
    public function changeStatusCarAction() {
        //http://admin.localhost.it//logistic/change-status-car

        $a = "";
        error_log("-----------------------------------------------------", 0);
        error_log("+++++++++++++++++changeStatusCarAction++++++++++++++++", 0);
        error_log("++++++++++++++++++++++++INIZIO++++++++++++++++++++++++", 0);
        error_log("-----------------------------------------------------", 0);
        //if($_SERVER['REMOTE_ADDR'] == '185.81.1.24'){
        //if($_SERVER['REMOTE_ADDR'] == '192.168.146.1'){
        if($_SERVER['REMOTE_ADDR'] == '172.16.254.3'){
            //recupera vari oggetti dai dati
            
            //$webuser = $this->webusersService->findById(96);
            //$webuser = new \SharengoCore\Entity\Webuser();
            $webuser = $this->webusersService->findById(96);
            error_log("+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+", 0);
            error_log($webuser->getDisplayName(), 0);
            //error_log($webuser->getId(), 0);
            error_log("+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+", 0);
            
            $car = $this->carsService->getCarByPlate($this->params()->fromPost('plate'));
            //$car = $this->carsService->getCarByPlate("TELA335");
            error_log("+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+", 0);
            error_log($car->getPlate(), 0);
            error_log("+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+", 0);
            $lastStatus = $car->getStatus();
            $car->setStatus($this->params()->fromPost('status'));
            //$car->setStatus('maintenance');
            if ($this->params()->fromPost('status') != "operative") {
                $postData['location'] = $this->params()->fromPost('location');
                //$postData['location'] = 'garage';
                error_log("+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+", 0);
                error_log($postData['location'], 0);
                error_log("+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+", 0);

                $postData['motivation'] = $this->params()->fromPost('motivation');
                //$postData['motivation'] = 1;
                error_log("+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+", 0);
                error_log($postData['motivation'], 0);
                error_log("+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+", 0);
            }
            $postData['note'] = $this->params()->fromPost('note');
            //$postData['note'] = 'note';
            error_log("+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+", 0);
            error_log($postData['note'], 0);
            error_log("+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+", 0);
            
             
            //chiamare il metodo $this->carsService->updateCar(...)
            $this->carsService->updateCar($car, $lastStatus, $postData, $webuser);
            //da fare?!
            $this->carsService->saveData($car, false);
            //risposta
            error_log("fine_____", 0);
            $response = $this->getResponse();
            $response->setStatusCode(200);
            $response->setContent(json_encode(array("response" => "Auto modificata con successo!")));
            return $response;
            
        } else {
            
        
            $postData['note'] = $this->params()->fromPost('note');
            error_log("+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+", 0);
            error_log("NON AUTORIZZATO", 0);
            error_log("+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+", 0);
            
            $response = $this->getResponse();
            $response->setStatusCode(401);
            return $response;
        }
        
        error_log("-----------------------------------------------------", 0);
        error_log("+++++++++++++++++changeStatusCarAction++++++++++++++++", 0);
        error_log("+++++++++++++++++++++++++FINE+++++++++++++++++++++++++", 0);
        error_log("-----------------------------------------------------", 0);
    }
    
    public function motivationAction() {
        if($_SERVER['REMOTE_ADDR'] == '185.81.1.24'){
            $motivation = $this->maintenanceMotivationsService->getAllMaintenanceMotivations();
            $response = $this->getResponse();
            $response->setStatusCode(200);
            $response->setContent(json_encode($motivation));
            return $response;
        } else {
            $response = $this->getResponse();
            $response->setStatusCode(401);
            return $response;
        }
    }

}

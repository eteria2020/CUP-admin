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
     * @var array
     */
    private $logisticConfig;

    /**
     * @param CarsService $carsService
     * @param WebusersService $webusersService
     * array $logisticConfig
     */
    public function __construct(
    CarsService $carsService, WebusersService $webusersService, MaintenanceMotivationsService $maintenanceMotivationsService, array $logisticConfig
    ) {
        $this->carsService = $carsService;
        $this->webusersService = $webusersService;
        $this->maintenanceMotivationsService = $maintenanceMotivationsService;
        $this->$logisticConfig = $logisticConfig;
    }
    
    public function changeStatusCarAction() {
        if($_SERVER['REMOTE_ADDR'] == $this->logisticConfig['url_logistic']){
            //user logistic
            $webuser = $this->webusersService->findByEmail($this->logisticConfig['email_logistic']);            
            $car = $this->carsService->getCarByPlate($this->params()->fromPost('plate'));
            $lastStatus = $car->getStatus();
            $car->setStatus($this->params()->fromPost('status'));
            if ($this->params()->fromPost('status') != "operative") {
                $postData['location'] = $this->params()->fromPost('location');
                $postData['motivation'] = $this->params()->fromPost('motivation');
            }
            $postData['note'] = $this->params()->fromPost('note');
            
            $this->carsService->updateCar($car, $lastStatus, $postData, $webuser);
            $this->carsService->saveData($car, false);
            
            $response = $this->getResponse();
            $response->setStatusCode(200);
            $response->setContent(json_encode(array("response" => "Auto modificata con successo!")));
            return $response;
        } else {
            $response = $this->getResponse();
            $response->setStatusCode(401);
            return $response;
        }
    }
    
    public function motivationAction() {
        if($_SERVER['REMOTE_ADDR'] == $this->logisticConfig['url_logistic']){
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

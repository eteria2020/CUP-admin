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
     * @param array $logisticConfig
     */
    public function __construct(
    CarsService $carsService, WebusersService $webusersService, MaintenanceMotivationsService $maintenanceMotivationsService, $logisticConfig
    ) {
        $this->carsService = $carsService;
        $this->webusersService = $webusersService;
        $this->maintenanceMotivationsService = $maintenanceMotivationsService;
        $this->logisticConfig = $logisticConfig;
    }

    public function changeStatusCarAction() {

        $params = json_decode(base64_decode($this->params()->fromPost('param')), true);

        if (isset($params['plate']) && isset($params['status']) && isset($params['location']) && isset($params['motivation']) && isset($params['note'])) {
            //user logistic
            $webuser = $this->webusersService->findByEmail($this->logisticConfig['email_logistic']);
            $car = $this->carsService->getCarByPlate($params['plate']);
            $lastStatus = $car->getStatus();
            $car->setStatus($params['status']);
            if ($params['status'] != "operative") {
                $postData['location'] = $params['location'];
                $postData['motivation'] = $params['motivation'];
            }
            $postData['note'] = $params['note'];

            $this->carsService->updateCar($car, $lastStatus, $postData, $webuser, true);
            $this->carsService->saveData($car, false);

            $response = $this->getResponse();
            $response->setStatusCode(200);
            $response->setContent(json_encode(array("response" => "Auto modificata con successo!")));
            return $response;
        } else {
            $response = $this->getResponse();
            $response->setStatusCode(400);
            $response->setContent(json_encode(array("response" => "Parametri mancanti")));
            return $response;
        }
    }

    /*
      public function motivationAction() {
      if($this->logisticConfig['type'] == $this->params()->fromPost('type')){
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
     */
    
     public function getLastMaintenanceCarAction() {
          
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(json_encode(array("response" => "TEST GET")));
        return $response;
        /*$params = json_decode(base64_decode($this->params()->fromPost('param')), true);   
        if (isset($params['plate'])) {
            $car_maintenance = $this->carsService->getLastMaintenanceCar($params['plate']);
            if(count($car_maintenance)>0){
                $response = $this->getResponse();
                $response->setStatusCode(200);
                $response->setContent(
                        json_encode(
                                array(
                                    "location" => $car_maintenance->getLocation(),
                                    "motivation" => $car_maintenance->getMotivation()->getDescription(),
                                    "note" => $car_maintenance->getNotes(),
                                )
                        )
                );
                return $response;
            }else{
                $response = $this->getResponse();
                $response->setStatusCode(200);
                $response->setContent(json_encode(array("response" => "Plate not found")));
                return $response;
            }
        } else {
            $response = $this->getResponse();
            $response->setStatusCode(400);
            $response->setContent(json_encode(array("response" => "Parametri mancanti")));
            return $response;
        }
    */
    }
}

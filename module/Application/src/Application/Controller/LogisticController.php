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
        $param = base64_decode($this->params()->fromPost('param'));
        $param = explode('&', $param);
        foreach ($param as $p){
            $p = explode('=', $p);
            $params[$p[0]] = $p[1];
        }
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

        $this->carsService->updateCar($car, $lastStatus, $postData, $webuser);
        $this->carsService->saveData($car, false);

        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(json_encode(array("response" => "Auto modificata con successo!")));
        return $response;
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

}

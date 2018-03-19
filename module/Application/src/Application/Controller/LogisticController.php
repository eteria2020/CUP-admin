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
        //$param = "type=ETR&plate=EH43994&status=maintenance&location=Carrozzeria Merciai, via del Pratellino 27/31, 50124 Firenze&motivation=1&note=[csadmin] AABBCC";
        $param = split('&', $param);
        foreach ($param as $p){
            $p = split('=', $p);
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

    /**
     * Decrypt data from a CryptoJS json encoding string
     *
     * @param mixed $passphrase
     * @param mixed $jsonString
     * @return mixed
     */
    function cryptoJsAesDecrypt($passphrase, $jsonString) {
        $jsondata = json_decode($jsonString, true);
        $salt = hex2bin($jsondata["s"]);
        $ct = base64_decode($jsondata["ct"]);
        $iv = hex2bin($jsondata["iv"]);
        $concatedPassphrase = $passphrase . $salt;
        $md5 = array();
        $md5[0] = md5($concatedPassphrase, true);
        $result = $md5[0];
        for ($i = 1; $i < 3; $i++) {
            $md5[$i] = md5($md5[$i - 1] . $concatedPassphrase, true);
            $result .= $md5[$i];
        }
        $key = substr($result, 0, 32);
        $data = openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
        return json_decode($data, true);
    }

}

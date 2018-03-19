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
        error_log("PARAM----------------------");
        error_log($this->params()->fromPost('param'));
        $str = $this->cryptoJsAesDecrypt("koala", $this->params()->fromPost('param'));
        error_log("STR DECRIPT----------------------");
        error_log($str);
        error_log("----------------------");
        /*
        if($this->logisticConfig['type'] == $this->params()->fromPost('type')){
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
         * 
         */
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
        try {
            $salt = hex2bin($jsondata["s"]);
            $iv = hex2bin($jsondata["iv"]);
        } catch (Exception $e) {
            return null;
        }
        $ct = base64_decode($jsondata["ct"]);
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

<?php
	
namespace Reports\Controller;

// Internal Modules
use Reports\Service\ReportsService;

// External Modules
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\Http\Response;
use Zend\Form\Form;
use DateTime;
use DateInterval;

class ApiController extends AbstractActionController
{
	/**
	 * @var ReportsService
	 */
	private $reportsService;	

	public function __construct(ReportsService $reportsService)
	{
		$this->reportsService = $reportsService;
	}	

	/**
	 * @return \Zend\Http\Response	(JSON Format)
	 */
	public function getCitiesAction()
	{
		// Get the cities, in JSON format
		$cities = $this->reportsService->getCities();
		
		// So, we don't need to use a JsonModel,but simply use an Http Response
		$this->response->setContent($cities);
		
		return $this->response;
	}
	
	/**
	 * @return \Zend\Http\Response	(CSV Format)
	 */
	public function getAllTripsAction()
	{
		$start_date = "";
		$end_date	= "";
		
		// Getting Post vars
		if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();
            
            if (!isset($postData["end_date"])) {
	            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);
	            return false;
	        }else{
		        $end_date = $postData["end_date"];
	        }
	        
	        // If the start_date is null, will set it with 30 days before the end_date
	        if (!isset($postData["start_date"]) || $postData["start_date"] == "") {
				$date  = new DateTime($end_date);
				$interval = new DateInterval('P30D');
				
				$date->sub($interval);
				$start_date = $date->format('d-m-Y 23:59:59');
	        }else{
		        $start_date = $postData["start_date"];
	        }

	    }else{
	        $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);
	        return false;
        }   
        
        // Get the trips, in array format
		$trips = $this->reportsService->getAllTrips($start_date,$end_date);
		
		// Try to convert the array in CSV using a temp file in memory
		try {
			// Generate CSV in Memory
			$file = fopen('php://temp/maxmemory:'. (12*1024*1024), 'r+'); // 128mb
			
			// Write CSV to memory
			fputcsv($file, array_keys(call_user_func_array('array_merge', $trips)));
			foreach($trips as $row)
			{
			    fputcsv($file, $row);
			}
			
			// Fetch CSV contents
			rewind($file);
			$output = stream_get_contents($file);
			fclose($file);
			
		} catch (\Exception $e) {	
			$this->flashMessenger()->addErrorMessage('Si è verificato un errore applicativo. L\'assistenza tecnica è già al corrente, ci scusiamo per l\'inconveniente');
			return false;
		}
				
		// Setting the header to return a CSV file
		$response = $this->getResponse();
		$response->getHeaders()
			->addHeaderLine('Content-Type', 'text/csv')
			->addHeaderLine('Content-Disposition', "attachment; filename=\"trips.csv\"")
			->addHeaderLine('Accept-Ranges', 'bytes')
			->addHeaderLine('Content-Length', strlen($output));
			
	    $response->setContent($output);
	    
	    return $response;     
	}
	
	public function getCityTripsAction()
	{
		$start_date = "";
		$end_date	= "";
		$city		= "";
		
		// Getting Post vars
		if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();
            
            if (!isset($postData["end_date"]) && !isset($postData["city"])) {
	            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);
	            return false;
	        }else{
		        $end_date = $postData["end_date"];
		        $city = $postData["city"];
	        }
	        
	        // If the start_date is null, will set it with 30 days before the end_date
	        if (!isset($postData["start_date"]) || $postData["start_date"] == "") {
				$date  = new DateTime($end_date);
				$interval = new DateInterval('P30D');
				
				$date->sub($interval);
				$start_date = $date->format('d-m-Y 23:59:59');
	        }else{
		        $start_date = $postData["start_date"];
	        }

	    }else{
	        $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);
	        return false;
        }   
		
		// Get the trips, in array format
		$trips = $this->reportsService->getCityTrips($start_date,$end_date,$city);
		
		// Try to convert the array in CSV using a temp file in memory
		try {
			// Generate CSV in Memory
			$file = fopen('php://temp/maxmemory:'. (12*1024*1024), 'r+'); // 128mb
			
			// Write CSV to memory
			fputcsv($file, array_keys(call_user_func_array('array_merge', $trips)));
			foreach($trips as $row)
			{
			    fputcsv($file, $row);
			}
			
			// Fetch CSV contents
			rewind($file);
			$output = stream_get_contents($file);
			fclose($file);
			
		} catch (\Exception $e) {	
			$this->flashMessenger()->addErrorMessage('Si è verificato un errore applicativo. L\'assistenza tecnica è già al corrente, ci scusiamo per l\'inconveniente');
			return false;
		}
				
		// Setting the header to return a CSV file
		$response = $this->getResponse();
		$response->getHeaders()
			->addHeaderLine('Content-Type', 'text/csv')
			->addHeaderLine('Content-Disposition', "attachment; filename=\"citytrips.csv\"")
			->addHeaderLine('Accept-Ranges', 'bytes')
			->addHeaderLine('Content-Length', strlen($output));
			
	    $response->setContent($output);
	    
	    return $response;   
	}
	
	/**
	 * @return \Zend\Http\Response	(JSON Format)
	 */
	public function getUrbanAreasAction()
	{
		$city = $this->params()->fromRoute('city', 0);
		
		if (!isset($city)) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);
            return false;
        }
        
		// Get the cities, in JSON format
		$urbanareas = $this->reportsService->getUrbanAreas($city);
		
		// So, we don't need to use a JsonModel,but simply use an Http Response
		$this->response->setContent($urbanareas);
		
		return $this->response;
	}
	
	public function getTripsGeoDataAction()
	{
		$start_date = "";
		$end_date	= "";
		$begend		= -1;
		
		// Getting Post vars
		if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();
            
            if (!isset($postData["end_date"]) && !isset($postData["begend"])) {
	            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);
	            return false;
	        }else{
		        $end_date 	= $postData["end_date"];
		        $begend 	= $postData["begend"];
	        }
	        
	        // If the start_date is null, will set it with 30 days before the end_date
	        if (!isset($postData["start_date"]) || $postData["start_date"] == "") {
				$date  		= new DateTime($end_date);
				$interval 	= new DateInterval('P30D');
				
				$date->sub($interval);
				$start_date = $date->format('d-m-Y 23:59:59');
	        }else{
		        $start_date = $postData["start_date"];
	        }

	    }else{
	        $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);
	        return false;
        }   
		
		// Get the trips geo data, in JSON format
		$tripsgeodata = $this->reportsService->getTripsGeoData($start_date,$end_date,$begend);
		
		// So, we don't need to use a JsonModel,but simply use an Http Response
		$this->response->setContent($tripsgeodata);
		
		return $this->response;
	}
	
	public function getCarsGeoDataAction()
	{
		if (!$this->getRequest()->isPost()) {
			$this->getResponse()->setStatusCode(Response::STATUS_CODE_404);
	        return false;
        }   
		
		// Get the trips geo data, in JSON format
		$carsgeodata = $this->reportsService->getCarsGeoData($start_date,$end_date,$begend);
		
		// So, we don't need to use a JsonModel,but simply use an Http Response
		$this->response->setContent($carsgeodata);
		
		return $this->response;
	}
	
	public function getTripsFromLogsAction()
	{
		$start_date = "";
		$end_date	= "";
		
		// Getting Post vars
		if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();
            
            if (!isset($postData["end_date"])) {
	            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);
	            return false;
	        }else{
		        $end_date 	= $postData["end_date"];
	        }
	        
	        // If the start_date is null, will set it with 30 days before the end_date
	        if (!isset($postData["start_date"]) || $postData["start_date"] == "") {
				$date  		= new DateTime($end_date);
				$interval 	= new DateInterval('P1D');
				
				$date->sub($interval);
				$start_date = $date->format('d-m-Y 23:59:59');
	        }else{
		        $start_date = $postData["start_date"];
	        }

	    }else{
	        $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);
	        return false;
        }
        
		// Get the trips data, in JSON format
		$tripsdata = $this->reportsService->getTripsFromLogs($start_date,$end_date);
		
		// So, we don't need to use a JsonModel,but simply use an Http Response
		$this->response->setContent(print_r($tripsdata,true));
		
		return $this->response;
	}
	
	public function getTripPointsFromLogsAction()
	{
		$trips_id = "";//"3942";
		
		// Getting Post vars
		if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();
            
            if (!isset($postData["trips_id"])) {
	            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);
	            return false;
	        }else{
		        $trips_id 	= $postData["trips_id"];
	        }

	    }else{
	        $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);
	        return false;
        } 
        
		// Get the trips data, in JSON format
		$tripsdata = $this->reportsService->getTripPointsFromLogs($trips_id);
		
		$this->response->getHeaders()
			->addHeaderLine('Accept-Ranges', 'bytes')
			->addHeaderLine('Content-Length', strlen($tripsdata))
			->addHeaderLine('Content-Type', 'application/force-download')
			->addHeaderLine('Content-Disposition', "attachment; filename=\"GPX.gpx\"")
			->addHeaderLine('Content-Transfer-Encoding', 'binary')
			->addHeaderLine('Cache-Control', 'no-cache, must-revalidate')
			;
    		
		// So, we don't need to use a JsonModel,but simply use an Http Response
		$this->response->setContent($tripsdata);
		
		return $this->response;
	}
}

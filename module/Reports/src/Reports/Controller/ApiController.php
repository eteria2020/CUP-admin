<?php
	
namespace Reports\Controller;

// Internal Modules
use Reports\Service\ReportsService;

// External Modules
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

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

	public function getCitiesAction()
	{
		// Get the cities, in JSON format
		$cities = $this->reportsService->getCities();
		
		// So, considering that JsonModel constructor convert an array to JSON
		// we need to decode our json to an array before passing it as parameter to JsonModel constructor 
		return new JsonModel(json_decode($cities, true));
	}
	
	public function getAllTripsAction()
	{
		
	}
	
	public function getCityTripsAction()
	{
		
	}
}

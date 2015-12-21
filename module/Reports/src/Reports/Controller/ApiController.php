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
		$cities = $this->reportsService->getCities();

		return new JsonModel(json_decode($cities[0]['row_to_json'], true));
	}
	
	public function getAllTripsAction()
	{
		
	}
	
	public function getCityTripsAction()
	{
		
	}
}

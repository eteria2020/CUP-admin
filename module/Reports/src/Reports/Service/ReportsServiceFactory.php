<?php
	
namespace Reports\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use MongoDB\Driver\Manager;

class ReportsServiceFactory implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		//$database = $serviceLocator->get('doctrine.entitymanager.orm_default');
		$database = $serviceLocator->get('doctrine.connection.orm_default');
		
		$mongodb = new Manager("mongodb://127.0.0.1:27017");
		$mongodb = $mongodb->sharengo;
		
		return new ReportsService($database,$mongodb);
	}
}

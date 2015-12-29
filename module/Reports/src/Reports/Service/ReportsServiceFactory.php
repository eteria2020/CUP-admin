<?php
	
namespace Reports\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


use MongoDB;


class ReportsServiceFactory implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		//$database = $serviceLocator->get('doctrine.entitymanager.orm_default');
		$database = $serviceLocator->get('doctrine.connection.orm_default');
		
		$mongodb = new MongoDB\Driver\Manager("mongodb://core.sharengo.it:27017");
		
		return new ReportsService($database,$mongodb);
	}
}

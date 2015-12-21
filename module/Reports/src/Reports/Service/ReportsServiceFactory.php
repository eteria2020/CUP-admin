<?php
	
namespace Reports\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ReportsServiceFactory implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		//$database = $serviceLocator->get('doctrine.entitymanager.orm_default');
		$database = $serviceLocator->get('doctrine.connection.orm_default');
		
		return new ReportsService($database);
	}
}

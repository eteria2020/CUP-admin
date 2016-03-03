<?php

namespace Reports\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use MongoDB;
use phpGpx;

if (!class_exists('phpGpx')) {
    include_once 'phpGPX.php';
}

class ReportsServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $database = $serviceLocator->get('doctrine.connection.orm_default');

        $mongoConfig = $serviceLocator->get('Configuration')['mongo'];
        $mongoConnectionString = 'mongodb://'.$mongoConfig['server'].':'.$mongoConfig['port'];
        $mongodb = new MongoDB\Driver\Manager($mongoConnectionString);

        return new ReportsService($database, $mongodb);
    }
}

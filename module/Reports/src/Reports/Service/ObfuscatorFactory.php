<?php
	
namespace Reports\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Assetic\Filter\UglifyJs2Filter;


class ObfuscatorFactory implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		// Set the uglify path		
		$uglifybin = realpath(__DIR__ . '/../../../../../').'/node_modules/uglify-js/bin/uglifyjs';
		
		// Set the node.js path
		$NodeJsOsXPath = '/usr/local/bin/node';
		$NodeJsLinuxPath = '/usr/lib/nodejs';
		
		$nodejsbin = file_exists($NodeJsLinuxPath) ? $NodeJsLinuxPath : $NodeJsOsXPath;

		$module =  new UglifyJs2Filter($uglifybin,$nodejsbin);
		
		// Set the params
        $module->setMangle(true);
        $module->setWrap(true);
        
        $module->setCompress("sequences=true, properties=true, dead_code=true, drop_debugger=true, conditionals=true, comparisons=true, evaluate=true, booleans=true, loops=true, unused=true, hoist_funs=true, hoist_vars=true, if_return=true, join_vars=true, cascade=true, side_effects=true, warnings=true ");			
		
		return $module;
	}
}

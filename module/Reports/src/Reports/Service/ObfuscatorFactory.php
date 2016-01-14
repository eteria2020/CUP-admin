<?php

namespace Reports\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Assetic\Filter\UglifyJs2Filter;

class ObfuscatorFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        // Get paths
        $nodeJsBinPath = realpath($serviceLocator->get('Configuration')['reports']['nodeJsBinPath']);
        $uglifyJsPath = realpath($serviceLocator->get('Configuration')['reports']['uglifyJsPath']);

        $module = new UglifyJs2Filter($uglifyJsPath, $nodeJsBinPath);

        // Set the params
        $module->setMangle(true);
        $module->setWrap(true);

        $module->setCompress('sequences=true, properties=true, dead_code=true, drop_debugger=true, conditionals=true, comparisons=true, evaluate=true, booleans=true, loops=true, unused=true, hoist_funs=true, hoist_vars=true, if_return=true, join_vars=true, cascade=true, side_effects=true, warnings=true ');

        return $module;
    }
}

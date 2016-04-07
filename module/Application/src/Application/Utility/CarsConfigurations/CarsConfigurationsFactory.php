<?php

namespace Application\Utility\CarsConfigurations;

class CarsConfigurationsFactory
{
    public static function create($configType, $configValue) {
        $configType = 'Application\\Utility\\CarsConfigurations\\'.$configType;
        
        if ( class_exists($configType) ) {
            return new $configType($configValue);
        }

        //@todo return new GenericConfigClass
    }
}
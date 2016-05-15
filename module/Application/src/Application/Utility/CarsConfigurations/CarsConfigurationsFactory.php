<?php

namespace Application\Utility\CarsConfigurations;

// Externals
use Zend\Mvc\I18n\Translator;

class CarsConfigurationsFactory
{
    public static function create($configType, $configValue, Translator $translator)
    {
        $configType = 'Application\\Utility\\CarsConfigurations\\'.$configType;

        if (class_exists($configType)) {
            return new $configType(
                $configValue,
                $translator
            );
        }

        //@todo return new GenericConfigClass
    }
}

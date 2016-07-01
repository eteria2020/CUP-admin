<?php

namespace Application\Utility\CarsConfigurations;

// Internals
use SharengoCore\Entity\CarsConfigurations;
use Application\Utility\CarsConfigurations\GenericCarConfiguration;
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

        return new GenericCarConfiguration(
            $configValue,
            $translator
        );
    }

    public static function createFromCarConfiguration(
        CarsConfigurations $carConfiguration,
        Translator $translator
    ) {
        return self::create(
            $carConfiguration->getKey(),
            $carConfiguration->getValue(),
            $translator
        );
    }
}

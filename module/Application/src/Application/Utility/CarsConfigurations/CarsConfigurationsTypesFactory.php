<?php

namespace Application\Utility\CarsConfigurations;

// Internals
use SharengoCore\Entity\CarsConfigurations;
use Application\Utility\CarsConfigurations\Types\GenericCarConfiguration;
// Externals
use Zend\Mvc\I18n\Translator;

class CarsConfigurationsTypesFactory
{
    /**
     * Create specific CarsConfigurationsType Utility Class
     *
     * @param string $configType
     * @param mixed $configValue
     * @param Translator $translator
     *
     * @return CarsConfigurationsTypesInterface
     */
    public static function create(
        $configType,
        $configValue,
        Translator $translator
    ) {
        $configType = 'Application\\Utility\\CarsConfigurations\\Types\\'.$configType;

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

    /**
     * Create specific CarsConfigurations Utility Class from Form
     *
     * @param CarsConfigurations $carConfiguration
     * @param Translator $translator
     *
     * @return CarsConfigurationsTypesInterface
     */
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

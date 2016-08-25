<?php

namespace Application\Utility\CarsConfigurations;

// Internals
use Application\Utility\CarsConfigurations\CarsConfigurationsTypesInterface;

interface CarsConfigurationsSingleValueTypesInterface extends CarsConfigurationsTypesInterface
{
    /**
     * It's a single value config.
     */
    const HAS_MULTIPLE_VALUES = false;
}

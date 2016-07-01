<?php

namespace Application\Utility\CarsConfigurations;

// Internals
use Application\Utility\CarsConfigurations\CarsConfigurationsTypesInterface;

interface CarsConfigurationsMultiValuesTypesInterface extends CarsConfigurationsTypesInterface
{
    /**
     * It's a multi value config.
     */
    const hasMultipleValues = true;

    /**
     * This method return an indexed values array.
     * The index is relative to configuration type.
     *
     * return mixed
     */
    public function getIndexedValueOptions();

    /**
     * This method delete an option from the value property.
     * 
     * @param mixed $optionId  The option id to be deleted.
     */
    public function deleteValueOption($optionId);
}
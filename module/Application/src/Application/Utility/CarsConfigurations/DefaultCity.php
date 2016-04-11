<?php

namespace Application\Utility\CarsConfigurations;

use Application\Form\CarsConfigurations\DefaultCityForm;

class DefaultCity implements CarsConfigurationsInterface
{
    private $value;

    /**
     * UseExternalGPSInterface constructor.
     */
    public function __construct($rawValue)
    {
        $this->value = $rawValue;
    }

    public function getOverview()
    {
        return $this->value;
    }

    public function getForm()
    {
        return new DefaultCityForm();
    }

    public function hasMultipleValues() 
    {
        return false;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getValueFromForm(array $data)
    {
        return $data['DefaultCity'];
    }

    public function getIndexedValues()
    {
        
    }
}
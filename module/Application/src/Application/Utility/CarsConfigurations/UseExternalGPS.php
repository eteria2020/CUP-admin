<?php

namespace Application\Utility\CarsConfigurations;

use Application\Form\CarsConfigurations\UseExternalGPSForm;

class UseExternalGPS implements CarsConfigurationsInterface
{
    private $value;

    /**
     * UseExternalGPSInterface constructor.
     */
    public function __construct($rawValue)
    {
        $this->value = json_decode($rawValue, true);
    }

    public function getOverview()
    {
        return $this->value ? 'Esterno' : 'Interno';
    }

    public function getForm()
    {
        return new UseExternalGPSForm();
    }

    public function hasMultipleValues() 
    {
        return false;
    }

    public function getDetails()
    {
        return $this->value ? 'Esterno' : 'Interno';
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getValueFromForm(array $data)
    {
        return $data['UseExternalGPS'];
    }

    public function getIndexedValues()
    {
        
    }
}
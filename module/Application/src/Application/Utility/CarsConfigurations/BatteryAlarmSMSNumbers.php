<?php

namespace Application\Utility\CarsConfigurations;

use Application\Form\CarsConfigurations\BatteryAlarmSMSNumbersForm;

class BatteryAlarmSMSNumbers implements CarsConfigurationsInterface
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
        $names = '';
        foreach($this->value as $value){
            $names .= '[ '.$value.' ] ';
        }
        return $names;
    }

    public function getForm()
    {
        return new BatteryAlarmSMSNumbersForm();
    }

    public function hasMultipleValues() 
    {
        return true;
    }

    public function getDetails()
    {
        
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getValueFromForm(array $data)
    {
        // Extract the radio configuration id.
        $id = $data['id'];
        unset($data['id']);

        // Get the complete record object
        $configuration = $this->getIndexedValues();
        $configurationUpdated = [];
        
        // Update the sepecific radio
        foreach($configuration as &$number){
            if($number['id'] === $id){
                $number = $data;
            }
            array_push($configurationUpdated,$number['number']);
        }

        // Recompose the json string.
        return json_encode($configurationUpdated);
    }

    public function getIndexedValues()
    {
        // Get the complete record object
        $configurations = $this->getValue();

        foreach ($configurations as &$configuration) {
            $configuration = [ 
                'number' => $configuration,
                'id' => strtolower(str_replace(' ', '' ,$configuration))
            ];
        }
        return $configurations;
    }
}
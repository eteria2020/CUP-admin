<?php

namespace Application\Utility\CarsConfigurations;

use Application\Form\CarsConfigurations\RadioSetupForm;

class RadioSetup implements CarsConfigurationsInterface
{
    private $value;

    /**
     * CarsConfigurationsInterface constructor.
     *
     * @param CarsConfigurationsService $carsConfigurationsService
     */
    public function __construct($rawValue)
    {
        $this->value = json_decode($rawValue, true);
    }

    public function getOverview()
    {
        $names = '';
        foreach($this->value as $value){
            $names .= '[ '.$value['name'].' ] ';
        }
        return $names;
    }

    public function getForm()
    {
        return new RadioSetupForm();
    }
    
    public function hasMultipleValues() 
    {
        return true;
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

        // Update the sepecific radio
        foreach($configuration as &$radio){
            if($radio['id'] === $id){
                $radio = $data;
            }
            // Remove the index from the radio configuration
            unset($radio['id']);
        }

        // Recompose the json string.
        return json_encode($configuration);
    }

    public function getIndexedValues()
    {
        // Get the complete record object
        $configurations = $this->getValue();

        foreach ($configurations as &$configuration) {
            $configuration['id'] = strtolower(str_replace(' ', '' ,$configuration['name']));
        }
        return $configurations;
    }
}
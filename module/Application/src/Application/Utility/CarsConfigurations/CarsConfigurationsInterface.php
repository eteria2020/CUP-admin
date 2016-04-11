<?php

namespace Application\Utility\CarsConfigurations;

interface CarsConfigurationsInterface
{
    public function __construct($rawValue);

    public function getOverview();

    public function getForm();

    public function getValue();
    
    public function hasMultipleValues();
    
     /**
     * This function return a string containing the value
     * field, updated with the data present into $data param.
     * 
     * @param   $data   array   The array containing the updated data from the edit form.
     *                          Example: [ "name" => "radioDeejay" , "volume" => 3 ] for a radio setup
     * @return String
     */
    public function getValueFromForm(array $data);

    /**
     * This function return an indexed values array.
     * The index is relative to configuration type.
     * This function is valid only for configuration
     * that have multiple options.
     */
    public function getIndexedValues();
}
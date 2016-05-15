<?php

namespace Application\Utility\CarsConfigurations;

// Externals
use Zend\Mvc\I18n\Translator;
use Zend\Form\Form;

interface CarsConfigurationsInterface
{
    /**
    * @param string $rawValue
    * @param Translator $translator
    */
    public function __construct(
        $rawValue,
        Translator $translator
    );

    /**
     * This method return a the overview value of the configuration
     *
     * @return string
     */
    public function getOverview();

    /**
     * This method return the specific configuration form.
     *
     * @return Form
     */
    public function getForm();

    /**
     * This method return a the value of the configuration
     *
     * @return mixed
     */
    public function getValue();

    /**
     * This method set the configuration instance value
     *
     * @param mixed $value
     */
    public function setValue($value);

    /**
     * This method return a the RAW (string) value of the configuration,
     * as saved in the CarsConfigurations instance.
     *
     * @return string
     */
    public function getRawValue();

    /**
     * This method return a defulat value for a new configuration
     *
     * @return mixed
     */
    public function getDefaultValue();

    /**
     * This method return "true" if the configuration has multiple values or
     * "false" if it's a single value config.
     *
     * @return boolean
     */
    public function hasMultipleValues();

    /**
     * This method return a string containing the value
     * field, updated with the data present into $data param.
     * 
     * @param array $data   The array containing the updated data from the edit form.
     *                      Example: [ "name" => "radioDeejay" , "volume" => 3 , ... ] for a radio setup
     * @return string
     */
    public function getValueFromForm(array $data);

    /**
     * This method return an indexed values array.
     * The index is relative to configuration type.
     * This method is valid only for configuration
     * that have multiple options.
     *
     * return mixed
     */
    public function getIndexedValues();
}

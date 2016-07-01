<?php

namespace Application\Utility\CarsConfigurations;

// Externals
use Zend\Mvc\I18n\Translator;
use Zend\Form\Form;

interface CarsConfigurationsTypesInterface
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
     * This method set the configuration instance value From
     * a give Raw Value.
     *
     * @param mixed $rawValue
     */
    public function setFromRawValue($rawValue);

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
     * This method update the value with the data present into $data param.
     * 
     * @param array $data   The array containing the updated data from the edit form.
     *                      Example: [ "name" => "radioDeejay" , "volume" => 3 , ... ] for a radio setup
     */
    public function updateValue(array $data);
}

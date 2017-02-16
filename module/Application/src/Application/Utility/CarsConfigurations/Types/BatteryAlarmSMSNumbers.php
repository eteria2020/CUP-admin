<?php

namespace Application\Utility\CarsConfigurations\Types;

// Internlas
use Application\Form\CarsConfigurations\BatteryAlarmSMSNumbersForm;
use Application\Utility\CarsConfigurations\CarsConfigurationsMultiValuesTypesInterface;
// Externals
use Zend\Mvc\I18n\Translator;

class BatteryAlarmSMSNumbers implements CarsConfigurationsMultiValuesTypesInterface
{
    /**
     * @var array
     */
    private $value;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * BatteryAlarmSMSNumbers constructor.
     *
     * @param string $rawValue
     * @param Translator $translator
     */
    public function __construct(
        $rawValue,
        Translator $translator
    ) {
        $this->setFromRawValue($rawValue);
        $this->translator = $translator;
    }

    public function getOverview()
    {
        $names = '';
        foreach ($this->value as $value) {
            $names .= '[ '.$value.' ] ';
        }
        return $names;
    }

    public function getForm()
    {
        return new BatteryAlarmSMSNumbersForm($this->translator);
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function setFromRawValue($rawValue)
    {
        $this->value = json_decode($rawValue, true);
    }

    public function getRawValue()
    {
        return json_encode($this->value);
    }

    public function getDefaultValue()
    {
        return ['3333333333'];
    }

    public function updateValue(array $data)
    {
        // Extract the radio configuration id.
        $id = $data['id'];
        unset($data['id']);

        // Get the complete record object
        $configurationOptions = $this->getIndexedValueOptions();
        $configurationOptionsUpdated = [];

        $newConfiguration = true;

        // Update the sepecific radio
        foreach ($configurationOptions as &$number) {
            if ($number['id'] === $id) {
                $number = $data;
                $newConfiguration = false;
            }
            array_push($configurationOptionsUpdated, $number['number']);
        }

        if ($newConfiguration) {
            // If this is a new configuration, we save it.
            array_push($configurationOptionsUpdated, $data['number']);
        }

        $this->setValue($configurationOptionsUpdated);
    }

    public function getIndexedValueOptions()
    {
        // Get the complete record object
        $configurations = $this->getValue();

        foreach ($configurations as &$configuration) {
            $configuration = [
                'number' => $configuration,
                'id' => strtolower(str_replace(' ', '', $configuration))
            ];
        }
        return $configurations;
    }

    public function deleteValueOption($optionId)
    {
        // Get the complete record object
        $configurationOptions = $this->getIndexedValueOptions();
        $configurationOptionsUpdated = [];

        // Remove the sepecific option
        foreach ($configurationOptions as $key => &$option) {
            if ($option['id'] !== $optionId) {
                array_push($configurationOptionsUpdated, $option['number']);
            }
        }

        $this->setValue($configurationOptionsUpdated);
    }
}

<?php

namespace Application\Utility\CarsConfigurations;

// Internlas
use Application\Form\CarsConfigurations\RadioSetupForm;
// Externals
use Zend\Mvc\I18n\Translator;

class RadioSetup implements CarsConfigurationsInterface
{
    /**
     * @var string
     */
    private $value;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * RadioSetup constructor.
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
            $names .= '[ '.$value['name'].' ] ';
        }
        return $names;
    }

    public function getForm()
    {
        return new RadioSetupForm($this->translator);
    }

    public function hasMultipleValues()
    {
        return true;
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
        return '[{"volume":"0","name":"Radio1","frequency":"0.0","band":"FM"},{"volume":0,"name":"Radio2","frequency":0.0,"band":"FM"},{"volume":0,"name":"Radio3","frequency":0.0,"band":"FM"},{"volume":0,"name":"Radio4","frequency":0.0,"band":"FM"}]';
    }

    public function updateValue(array $data)
    {
        // Extract the radio configuration id.
        $id = $data['id'];
        unset($data['id']);

        // Get the complete record object
        $configurationOptions = $this->getIndexedValueOptions();

        $newConfiguration = true;

        // Update the sepecific radio
        foreach ($configurationOptions as &$radio) {
            if ($radio['id'] === $id) {
                $radio = $data;
                $newConfiguration = false;
            }
            // Remove the index from the radio configuration
            unset($radio['id']);
        }

        if ($newConfiguration) {
            // If this is a new configuration, we save it.
            array_push($configurationOptions, $data);
        }

        $this->setValue($configurationOptions);
    }

    public function getIndexedValueOptions()
    {
        // Get the complete record object
        $configurations = $this->getValue();

        foreach ($configurations as &$configuration) {
            $configuration['id'] = strtolower(str_replace(' ', '', $configuration['name']));
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
                array_push($configurationOptionsUpdated, $option);
            }
        }

        $this->setValue($configurationOptionsUpdated);
    }
}

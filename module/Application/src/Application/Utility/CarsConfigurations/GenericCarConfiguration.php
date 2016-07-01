<?php

namespace Application\Utility\CarsConfigurations;

// Internlas
use Application\Form\CarsConfigurations\GenericCarConfigurationForm;
// Externals
use Zend\Mvc\I18n\Translator;

class GenericCarConfiguration implements CarsConfigurationsInterface
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
     * GenericCarConfiguration constructor.
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
        return substr($this->value,0,20) . '...';
    }

    public function getForm()
    {
        return new GenericCarConfigurationForm($this->translator);
    }

    public function hasMultipleValues()
    {
        return false;
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
        $this->setValue($rawValue);
    }

    public function getRawValue()
    {
        return (string) $this->value;
    }

    public function getDefaultValue()
    {
        return '{}';
    }

    public function updateValue(array $data)
    {
        $this->setValue($data['GenericCarConfiguration']);
    }

    public function getIndexedValueOptions()
    {
        return $this->value;
    }

    public function deleteValueOption($optionId) {}
}

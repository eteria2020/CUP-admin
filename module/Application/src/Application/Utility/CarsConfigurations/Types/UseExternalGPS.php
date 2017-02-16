<?php

namespace Application\Utility\CarsConfigurations\Types;

// Internlas
use Application\Form\CarsConfigurations\UseExternalGPSForm;
use Application\Utility\CarsConfigurations\CarsConfigurationsSingleValueTypesInterface;
// Externals
use Zend\Mvc\I18n\Translator;

class UseExternalGPS implements CarsConfigurationsSingleValueTypesInterface
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
     * UseExternalGPS constructor.
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
        return $this->value ? 'Esterno' : 'Interno';
    }

    public function getForm()
    {
        return new UseExternalGPSForm($this->translator);
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
        $this->value = $rawValue === 'true' ? 1 : 0;
    }

    public function getRawValue()
    {
        return $this->value ? 'true' : 'false';
    }

    public function getDefaultValue()
    {
        return true;
    }

    public function updateValue(array $data)
    {
        $this->setFromRawValue($data['value']);
    }
}

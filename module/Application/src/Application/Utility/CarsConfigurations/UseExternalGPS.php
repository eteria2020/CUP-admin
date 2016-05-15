<?php

namespace Application\Utility\CarsConfigurations;

// Internlas
use Application\Form\CarsConfigurations\UseExternalGPSForm;
// Externals
use Zend\Mvc\I18n\Translator;

class UseExternalGPS implements CarsConfigurationsInterface
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
        $this->setValue(json_decode($rawValue, true));
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

    public function getRawValue()
    {
        return (string) $this->value;
    }

    public function getDefaultValue()
    {
        return true;
    }

    public function getValueFromForm(array $data)
    {
        return $data['UseExternalGPS'];
    }

    public function getIndexedValues()
    {
        return $this->value;
    }
}

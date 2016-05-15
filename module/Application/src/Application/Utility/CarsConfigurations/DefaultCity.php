<?php

namespace Application\Utility\CarsConfigurations;

// Internlas
use Application\Form\CarsConfigurations\DefaultCityForm;
// Externals
use Zend\Mvc\I18n\Translator;

class DefaultCity implements CarsConfigurationsInterface
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
     * DefaultCity constructor.
     *
     * @param string $rawValue
     * @param Translator $translator
     */
    public function __construct(
        $rawValue,
        Translator $translator
    ) {
        $this->setValue($rawValue);
        $this->translator = $translator;
    }

    public function getOverview()
    {
        return $this->value;
    }

    public function getForm()
    {
        return new DefaultCityForm($this->translator);
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
        return 'Milano';
    }

    public function getValueFromForm(array $data)
    {
        return $data['DefaultCity'];
    }

    public function getIndexedValues()
    {
        return $this->value;
    }
}

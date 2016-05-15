<?php

namespace Application\Form\CarsConfigurations;

// Externals
use Zend\Form\Form;
use Zend\Mvc\I18n\Translator;

/**
 * Class UseExternalGpsForm
 * @package Application\Form
 */
class UseExternalGPSForm extends Form
{
    public function __construct(Translator $translator)
    {
        parent::__construct('carConfiguration');
        $this->setAttribute('method', 'post');

        $this->add([
            'name' => 'UseExternalGPS',
            'type' => 'Zend\Form\Element\Radio',
            'attributes' => [
                'id' => 'externalgps',
                'class' => 'form-control',
                'required' => 'required'
            ],
            'options' => [
                'value_options' => [
                    'false' => 'No',
                    'true' => 'Si',
                ],
                'label' => $translator->translate('Gps Esterno'),
            ]
        ]);
    }
}

<?php

namespace Application\Form\CarsConfigurations;

// Externals
use Zend\Form\Form;
use Zend\Mvc\I18n\Translator;

/**
 * Class DefaultCityForm
 */
class DefaultCityForm extends Form
{
    public function __construct(Translator $translator)
    {
        parent::__construct('carConfiguration');
        $this->setAttribute('method', 'post');

        $this->add([
            'name' => 'value',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'city',
                'class' => 'form-control',
                'required' => 'required'
            ],
            'options' => [
                'label' => $translator->translate('Citta\''),
            ],
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'value' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'NotEmpty',
                    ]
                ],
            ],
        ];
    }
}

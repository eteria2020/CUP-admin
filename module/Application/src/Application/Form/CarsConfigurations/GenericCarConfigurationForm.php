<?php

namespace Application\Form\CarsConfigurations;

// Externals
use Zend\Form\Form;
use Zend\Mvc\I18n\Translator;

/**
 * Class GenericCarConfigurationForm
 */
class GenericCarConfigurationForm extends Form
{
    public function __construct(Translator $translator)
    {
        parent::__construct('carConfiguration');
        $this->setAttribute('method', 'post');

        $this->add([
            'name' => 'value',
            'type' => 'Zend\Form\Element\Textarea',
            'attributes' => [
                'id' => 'value',
                'class' => 'form-control',
                'required' => 'required'
            ],
            'options' => [
                'label' => $translator->translate('Valore\''),
            ],
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'value' => [
                'required' => true,
                'filters'  => [
                    [
                        'name' => 'StringTrim'
                    ],
                ],
                'validators' => [
                    [
                        'name' => 'NotEmpty',
                    ]
                ],
            ],
        ];
    }
}

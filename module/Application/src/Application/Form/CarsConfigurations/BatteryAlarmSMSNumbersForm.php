<?php

namespace Application\Form\CarsConfigurations;

// Externals
use Zend\Form\Form;
use Zend\Mvc\I18n\Translator;

/**
 * Class BatteryAlarmSMSNumbersForm
 */
class BatteryAlarmSMSNumbersForm extends Form
{
    public function __construct(Translator $translator)
    {
        parent::__construct('carConfiguration');
        $this->setAttribute('method', 'post');

        $this->add([
            'name' => 'id',
            'type' => 'Zend\Form\Element\Hidden',
            'attributes' => [
                'id' => 'id',
            ],
        ]);

        $this->add([
            'name' => 'number',
            'type' => 'Zend\Form\Element\Number',
            'attributes' => [
                'id' => 'number',
                'class' => 'form-control',
                'required' => 'required'
            ],
            'options' => [
                'label' => $translator->translate('Numero'),
            ],
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'number' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'NotEmpty',
                    ],
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'max' => 20
                        ],
                    ],
                ],
            ],
        ];
    }
}

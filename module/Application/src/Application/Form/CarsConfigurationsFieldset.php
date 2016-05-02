<?php

namespace Application\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Mvc\I18n\Translator;
use Zend\Stdlib\Hydrator\HydratorInterface;

use SharengoCore\Entity\CarsConfigurations;

/**
 * Class CarsConfigurationsFieldset
 * @package Application\Form
 */
class CarsConfigurationsFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(Translator $translator, HydratorInterface $hydrator)
    {
        parent::__construct('carsConfigurations', [
            'use_as_base_fieldset' => true
        ]);


        $this->setHydrator($hydrator);
        $this->setObject(new CarsConfigurations());

        $this->add([
            'name'       => 'fleet',
            'type'       => 'Zend\Form\Element\Select',
            'attributes' => [
                'id'    => 'fleet',
                'class' => 'form-control',
            ],
            'options'    => [
                'value_options' => [
                    '' => ''
                ]
            ]
        ]);

        $this->add([
            'name' => 'model',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'model',
                'class'    => 'form-control',
                'maxlength' => 5,
                'placeholder' => $translator->translate('Modello Auto'),
            ]
        ]);

        $this->add([
            'name' => 'plate',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'plate',
                'class'    => 'form-control',
                'maxlength' => 5,
                'placeholder' => $translator->translate('Targa Auto'),
            ]
        ]);

        $this->add([
            'name' => 'key',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'key',
                'class'    => 'form-control',
                'maxlength' => 5,
                'placeholder' => $translator->translate('Tipo di Configurazione'),
                'required' => 'required'
            ]
        ]);
    }
    
    public function getInputFilterSpecification()
    {
        return [
            'fleet' => [
                'required' => false,
                'filters'  => [
                    [
                        'name' => 'StringTrim'
                    ],
                ],
            ],
            'model' => [
                'required' => false,
                'filters'  => [
                    [
                        'name' => 'StringTrim'
                    ],
                ],
            ],
            'plate' => [
                'required' => false,
                'filters'  => [
                    [
                        'name' => 'StringTrim'
                    ],
                ],
            ],
            'key' => [
                'required' => true,
                'filters'  => [
                    [
                        'name' => 'StringTrim'
                    ]
                ],
                'validators' => [
                    [
                        'name' =>'NotEmpty',
                    ]
                ],
            ],
        ];
    }
}

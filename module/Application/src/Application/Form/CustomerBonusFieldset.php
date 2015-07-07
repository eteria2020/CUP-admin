<?php

namespace Application\Form;

use SharengoCore\Entity\CustomersBonus;
use Zend\Stdlib\Hydrator\HydratorInterface;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class CustomerBonusFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct(HydratorInterface $hydrator) {

        parent::__construct('customer-bonus', [
            'use_as_base_fieldset' => true
        ]);

        $this->setHydrator($hydrator);
        $this->setObject(new CustomersBonus());

        $this->add([
            'name'       => 'total',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'       => 'total',
                'class'    => 'form-control',
                'required' => 'required'
            ]
        ]);

        $this->add([
            'name'       => 'type',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'       => 'type',
                'class'    => 'form-control',
                'required' => 'required'
            ]
        ]);

        $this->add([
            'name'       => 'description',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'       => 'type',
                'class'    => 'form-control',
                'required' => 'required'
            ]
        ]);

        $this->add([
            'name'       => 'validFrom',
            'type'       => 'Zend\Form\Element\Date',
            'attributes' => [
                'id'       => 'valid_from',
                'class'    => 'form-control date-picker',
                'required' => 'required'
            ]
        ]);

        $this->add([
            'name'       => 'validTo',
            'type'       => 'Zend\Form\Element\Date',
            'attributes' => [
                'id'       => 'valid_to',
                'class'    => 'form-control date-picker',
                'required' => 'required'
            ]
        ]);

        $this->add([
            'name'       => 'durationDays',
            'type'       => 'Zend\Form\Element\Number',
            'attributes' => [
                'id'       => 'valid_to',
                'class'    => 'form-control',
            ]
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'total' => [
                'required' => true,
                'filters'  => [
                    [
                        'name' => 'StringTrim'
                    ]
                ]
            ],
            'type' => [
                'required' => true,
                'filters'  => [
                    [
                        'name' => 'StringTrim'
                    ]
                ]
            ],
            'description' => [
                'required' => true,
                'filters'  => [
                    [
                        'name' => 'StringTrim'
                    ]
                ]
            ],
            'validFrom' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'Date'
                    ],
                ]
            ],
            'validTo' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'Date'
                    ],
                ]
            ],
            'durationDays' => [
                'required' => false
            ],

        ];
    }
}

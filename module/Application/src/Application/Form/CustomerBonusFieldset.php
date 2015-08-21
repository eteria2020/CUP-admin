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
            'type'       => 'Zend\Form\Element\Number',
            'attributes' => [
                'id'       => 'total',
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
            ]
        ]);

        $this->add([
            'name'       => 'validTo',
            'type'       => 'Zend\Form\Element\Date',
            'attributes' => [
                'id'       => 'valid_to',
                'class'    => 'form-control date-picker',
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
                ],
                'validators' => [
                    [
                        'name' => 'Int'
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
                'required' => false,
                'validators' => [
                    [
                        'name' => 'Date',
                        'options' => [
                            'format' => 'Y-m-d'
                        ],
                    ]
                ]
            ],
            'validTo' => [
                'required' => false,
                'validators' => [
                    [
                        'name' => 'Date',
                        'options' => [
                            'format' => 'Y-m-d'
                        ],
                    ],
                    [
                        'name'    => 'Callback',
                        'options' => [
                            'messages' => [
                                \Zend\Validator\Callback::INVALID_VALUE => 'La data di fine validità deve essere posteriore alla data di inizio',
                            ],
                            'callback' => function ($value, $context) {
                                $validFrom = date_create($context['validFrom']);
                                $validTo = date_create($value);

                                return $validFrom < $validTo ? true : false;
                            },
                        ],
                    ],
                ]
            ]
        ];
    }
}
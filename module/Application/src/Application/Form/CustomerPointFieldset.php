<?php

namespace Application\Form;

use SharengoCore\Entity\CustomersPoints;
use Zend\Mvc\I18n\Translator;
use Zend\Stdlib\Hydrator\HydratorInterface;
use SharengoCore\Service\AddPointService;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class CustomerPointFieldset extends Fieldset implements InputFilterProviderInterface
{

    private $translator;
    public function __construct(HydratorInterface $hydrator, Translator $translator, AddPointService $addPointService)
    {
        parent::__construct('customer-point', [
            'use_as_base_fieldset' => true
        ]);
        $this->addPointService = $addPointService;
        $this->translator = $translator;
        $this->setHydrator($hydrator);
        $this->setObject(new CustomersPoints());

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
            'type'       => 'Zend\Form\Element\Select',
            'attributes' => [
                'id'       => 'type',
                'class'    => 'form-control',
                'required' => 'required'
            ],
            'options' => [
                'value_options' => $addPointService->getAllAddPoint()
            ]
        ]);

        $this->add([
            'name'       => 'validFrom',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'       => 'valid_from',
                'class'    => 'form-control date-picker',
            ]
        ]);

        $this->add([
            'name'       => 'validTo',
            'type'       => 'Zend\Form\Element\Text',
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
                    ],
                    [
                        'name'    => 'Callback',
                        'options' => [
                            'messages' => [
                                \Zend\Validator\Callback::INVALID_VALUE => $this->translator->translate('Il valore massimo inseribile è 15 min.'),
                            ],
                            'callback' => function ($value) {
                                
                                return $value <= 15 ? true : false;
                            },
                        ],
                    ],
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
                                \Zend\Validator\Callback::INVALID_VALUE => $this->translator->translate('La data di fine validità deve essere posteriore alla data di inizio'),
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

    public function bindValues(array $values = [])
    {
        // the bonus is valid until the end of the day
        $values['validTo'] .= ' 23:59:59';

        return parent::bindValues($values);
    }
}

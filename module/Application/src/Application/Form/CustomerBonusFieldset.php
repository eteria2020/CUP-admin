<?php

namespace Application\Form;

use SharengoCore\Entity\CustomersBonus;
use Zend\Mvc\I18n\Translator;
use Zend\Stdlib\Hydrator\HydratorInterface;
use SharengoCore\Service\AddBonusService;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class CustomerBonusFieldset extends Fieldset implements InputFilterProviderInterface
{

    private $translator;
    private $config;
    private $roles;
    private $max_minutes = 15; // for normal user
    private $max_minutes_sa = 250; // for superadmin
    
    public function __construct(HydratorInterface $hydrator, Translator $translator, AddBonusService $addBonusService, $config, $roles)
    {
        parent::__construct('customer-bonus', [
            'use_as_base_fieldset' => true
        ]);
        $this->addBonusService = $addBonusService;
        $this->translator = $translator;
        $this->config = $config;
        $this->roles = $roles;
        
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
            'type'       => 'Zend\Form\Element\Select',
            'attributes' => [
                'id'       => 'type',
                'class'    => 'form-control',
                'required' => 'required'
            ],
            'options' => [
                'value_options' => $addBonusService->getAllAddBonus()
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
        $this->add([
            'name'       => 'note',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'       => 'note',
                'class' => 'form-control',
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
                                \Zend\Validator\Callback::INVALID_VALUE => $this->translator->translate('Il massimo valore inseribile è stato superato:') . ' ' . $this->max_minutes,
                            ],
                            'callback' => function ($value) {

                                if(isset($this->config['serverInstance']["id"])) {
                                    if($this->config['serverInstance']["id"]=="sk_SK") {
                                        return true;
                                    }
                                }
                                                                
                                if($this->roles[0]!=='superadmin' && $value >$this->max_minutes) {
                                    return false;
                                } else {
                                    return true;
                                }
                            },
                        ],
                    ],
                    [
                        'name'    => 'Callback',
                        'options' => [
                            'messages' => [
                                \Zend\Validator\Callback::INVALID_VALUE => $this->translator->translate('Il massimo valore inseribile è stato superato:') . ' ' . $this->max_minutes_sa,
                            ],
                            'callback' => function ($value) {

                                if(isset($this->config['serverInstance']["id"])) {
                                    if($this->config['serverInstance']["id"]=="sk_SK") {
                                        return true;
                                    }
                                }
                                if($this->roles[0]=='superadmin' && $value >$this->max_minutes_sa) {
                                    return false;
                                } else {
                                    return true;
                                }
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
            ],
            'note' => [
                'required' => false,
                'filters' => [
                    [
                        'name' => 'StringTrim',
                    ]
                ]
            ],
        ];
    }

    public function bindValues(array $values = [])
    {
        // the bonus is valid until the end of the day
        $values['validTo'] .= ' 23:59:59';

        return parent::bindValues($values);
    }
}

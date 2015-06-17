<?php
namespace Application\Form;

use SharengoCore\Entity\Webuser;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;

/**
 * Class UserFieldset
 * @package Application\Form
 */
class UserFieldset extends Fieldset implements InputFilterProviderInterface
{
    const EMAIL_NOT_VALID = 'Formato email non valido';

    /**
     * @param string $name
     * @param array  $options
     */
    public function __construct(HydratorInterface $hydrator)
    {
        parent::__construct('user', [
            'use_as_base_fieldset' => true
        ]);

        $this->setHydrator($hydrator);
        $this->setObject(new Webuser());

        $this->add([
            'name'       => 'id',
            'type'       => 'Zend\Form\Element\Hidden',
            'attributes' => [
                'id' => 'id'
            ]
        ]);

        $this->add([
            'name'       => 'email',
            'type'       => 'Zend\Form\Element\Email',
            'attributes' => [
                'id'       => 'email',
                'class'    => 'form-control',
                'required' => 'required'
            ]
        ]);

        $this->add([
            'name'       => 'email2',
            'type'       => 'Zend\Form\Element\Email',
            'attributes' => [
                'id'       => 'email2',
                'class'    => 'form-control',
                'required' => 'required'
            ]
        ]);

        $this->add([
            'name'       => 'displayName',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'       => 'displayName',
                'class'    => 'form-control',
                'required' => 'required'
            ]
        ]);

        $this->add([
            'name'       => 'password',
            'type'       => 'Zend\Form\Element\Password',
            'attributes' => [
                'id'       => 'password',
                'class'    => 'form-control',
            ]
        ]);

        $this->add([
            'name'       => 'password2',
            'type'       => 'Zend\Form\Element\Password',
            'attributes' => [
                'id'       => 'password',
                'class'    => 'form-control',
            ]
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'email' => [
                'required' => true,
                'validators' => [
                    [
                        'name' =>'NotEmpty',
                        'options' => [
                            'messages' => [
                                \Zend\Validator\NotEmpty::IS_EMPTY => "Il campo email non può essere vuoto"
                            ],
                        ],
                    ],
                    [
                        'name' => 'EmailAddress',
                        'break_chain_on_failure' => true,
                        'options' => [
                            'messages' => [
                                \Zend\Validator\EmailAddress::INVALID_FORMAT     => self::EMAIL_NOT_VALID,
                                \Zend\Validator\EmailAddress::INVALID            => self::EMAIL_NOT_VALID,
                                \Zend\Validator\EmailAddress::INVALID_HOSTNAME   => self::EMAIL_NOT_VALID,
                                \Zend\Validator\EmailAddress::INVALID_LOCAL_PART => self::EMAIL_NOT_VALID,
                                \Zend\Validator\EmailAddress::INVALID_MX_RECORD  => self::EMAIL_NOT_VALID,
                                \Zend\Validator\EmailAddress::DOT_ATOM           => self::EMAIL_NOT_VALID,
                                \Zend\Validator\EmailAddress::INVALID_SEGMENT    => self::EMAIL_NOT_VALID,
                                \Zend\Validator\EmailAddress::QUOTED_STRING      => self::EMAIL_NOT_VALID,
                                \Zend\Validator\EmailAddress::LENGTH_EXCEEDED    => self::EMAIL_NOT_VALID,
                            ],
                        ],
                    ]
                ]
            ],
            'email2' => [
                'required' => true,
                'validators' => [
                    [
                        'name' =>'NotEmpty',
                        'options' => [
                            'messages' => [
                                \Zend\Validator\NotEmpty::IS_EMPTY => "Il campo email non può essere vuoto"
                            ],
                        ],
                    ],
                    [
                        'name' => 'EmailAddress',
                        'break_chain_on_failure' => true,
                        'options' => [
                            'messages' => [
                                \Zend\Validator\EmailAddress::INVALID_FORMAT     => self::EMAIL_NOT_VALID,
                                \Zend\Validator\EmailAddress::INVALID            => self::EMAIL_NOT_VALID,
                                \Zend\Validator\EmailAddress::INVALID_HOSTNAME   => self::EMAIL_NOT_VALID,
                                \Zend\Validator\EmailAddress::INVALID_LOCAL_PART => self::EMAIL_NOT_VALID,
                                \Zend\Validator\EmailAddress::INVALID_MX_RECORD  => self::EMAIL_NOT_VALID,
                                \Zend\Validator\EmailAddress::DOT_ATOM           => self::EMAIL_NOT_VALID,
                                \Zend\Validator\EmailAddress::INVALID_SEGMENT    => self::EMAIL_NOT_VALID,
                                \Zend\Validator\EmailAddress::QUOTED_STRING      => self::EMAIL_NOT_VALID,
                                \Zend\Validator\EmailAddress::LENGTH_EXCEEDED    => self::EMAIL_NOT_VALID,
                            ],
                        ],
                    ],
                    [
                        'name' => 'Identical',
                        'options' => [
                            'token' => 'email',
                            'messages' => [
                                \Zend\Validator\Identical::NOT_SAME => "Le due email non coincidono"
                            ],
                        ]
                    ]
                ]
            ],
            'displayName' => [
                'required' => true,
                'filters'  => [
                    [
                        'name' => 'StringTrim'
                    ]
                ],
                'validators' => [
                    [
                        'name' =>'NotEmpty',
                        'options' => [
                            'messages' => [
                                \Zend\Validator\NotEmpty::IS_EMPTY => "Il campo nome & cognome non può essere lasciato vuoto"
                            ],
                        ],
                    ]
                ],
            ],
            'password' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ],
                'validators' => [
                    [
                        'name' =>'NotEmpty',
                        'options' => [
                            'messages' => [
                                \Zend\Validator\NotEmpty::IS_EMPTY => "La password non può essere lasciata vuota"
                            ],
                        ],
                    ],
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 8,
                            'messages' => [
                                \Zend\Validator\StringLength::TOO_SHORT => "La password deve essere min 8 caratteri"
                            ],
                        ]
                    ]
                ]
            ],
            'password2' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ],
                'validators' => [
                    [
                        'name' =>'NotEmpty',
                        'options' => [
                            'messages' => [
                                \Zend\Validator\NotEmpty::IS_EMPTY => "La password non può essere lasciata vuota"
                            ],
                        ],
                    ],
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 8,
                            'messages' => [
                                \Zend\Validator\StringLength::TOO_SHORT => "La password deve essere min 8 caratteri"
                            ],
                        ],
                        'break_chain_on_failure' => true
                    ],
                    [
                        'name' => 'Identical',
                        'options' => [
                            'token' => 'password',
                            'messages' => [
                                \Zend\Validator\Identical::NOT_SAME => "Le due password non coincidono"
                            ],
                        ]
                    ]
                ]
            ],
        ];
    }
}
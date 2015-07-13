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

        $this->add([
            'name'       => 'role',
            'type'       => 'Zend\Form\Element\Select',
            'attributes' => [
                'id'       => 'role',
                'class'    => 'form-control',
                'required' => 'required'
            ],
            'options'    => [
                'value_options' => [
                    'admin' => 'admin',
                    'callcenter' => 'callcenter'
                ]
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
                    ],
                    [
                        'name' => 'EmailAddress',
                        'break_chain_on_failure' => true,
                    ]
                ]
            ],
            'email2' => [
                'required' => true,
                'validators' => [
                    [
                        'name' =>'NotEmpty',
                    ],
                    [
                        'name' => 'EmailAddress',
                        'break_chain_on_failure' => true,
                    ],
                    [
                        'name' => 'Identical',
                        'options' => [
                            'token' => 'email',
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
                    ],
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 8
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
                    ],
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 8,
                        ],
                        'break_chain_on_failure' => true
                    ],
                    [
                        'name' => 'Identical',
                        'options' => [
                            'token' => 'password',
                        ]
                    ]
                ]
            ],
            'role' => [
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
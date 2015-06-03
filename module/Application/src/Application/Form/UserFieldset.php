<?php
namespace Application\Form;

use SharengoCore\Service\UsersService;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;
use ZfcUserDoctrineORM\Entity\User;

/**
 * Class UserFieldset
 * @package Application\Form
 */
class UserFieldset extends Fieldset implements InputFilterProviderInterface
{
    /**
     * @var UsersService
     */
    private $I_userService;

    /**
     * @param string $name
     * @param array  $options
     */
    public function __construct(UsersService $I_userService, HydratorInterface $hydrator)
    {
        parent::__construct('user', [
            'use_as_base_fieldset' => true
        ]);

        $this->I_userService = $I_userService;
        $this->setHydrator($hydrator);
        $this->setObject(new User());

        $this->add([
            'name'       => 'userId',
            'type'       => 'Zend\Form\Element\Hidden',
            'attributes' => [
                'id' => 'id'
            ]
        ]);

        $this->add([
            'name'       => 'username',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'       => 'username',
                'class'    => 'form-control',
                'required' => 'required',
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
                'required' => 'required'
            ]
        ]);

        $this->add([
            'name'       => 'password2',
            'type'       => 'Zend\Form\Element\Password',
            'attributes' => [
                'id'       => 'password',
                'class'    => 'form-control',
                'required' => 'required'
            ]
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'username'    => [
                'required' => true,
                'filters'  => [
                    [
                        'name' => 'StringTrim'
                    ]
                ]
            ],
            'email' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'EmailAddress',
                        'break_chain_on_failure' => true
                    ],
                    [
                        'name' => 'Application\Form\Validator\DuplicateEmail',
                        'options' => [
                            'service' => $this->I_userService
                        ]
                    ]
                ]
            ],
            'email2' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'EmailAddress',
                        'break_chain_on_failure' => true
                    ],
                    [
                        'name' => 'Identical',
                        'options' => [
                            'token' => 'email'
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
                ]
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
                        'name' => 'StringLength',
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
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 8
                        ],
                        'break_chain_on_failure' => true
                    ],
                    [
                        'name' => 'Identical',
                        'options' => [
                            'token' => 'password'
                        ]
                    ]
                ]
            ],
        ];
    }
}
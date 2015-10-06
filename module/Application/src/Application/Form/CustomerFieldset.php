<?php
namespace Application\Form;

use SharengoCore\Entity\Customers;
use SharengoCore\Service\CountriesService;
use SharengoCore\Service\CustomersService;
use SharengoCore\Service\ProvincesService;
use SharengoCore\Service\AuthorityService;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;

/**
 * Class CustomerFieldset
 * @package Application\Form
 */
class CustomerFieldset extends Fieldset implements InputFilterProviderInterface
{
    private $customersService;

    /**
     * @param string $name
     * @param array  $options
     */
    public function __construct(
        CustomersService $customersService,
        CountriesService $countriesService,
        ProvincesService $provincesService,
        HydratorInterface $hydrator
    ) {
        $this->customersService = $customersService;

        parent::__construct('customer', [
            'use_as_base_fieldset' => true
        ]);

        $this->setHydrator($hydrator);
        $this->setObject(new Customers());

        $this->add([
            'name'       => 'id',
            'type'       => 'Zend\Form\Element\Hidden',
            'attributes' => [
                'id' => 'id'
            ]
        ]);

        $this->add([
            'name'       => 'gender',
            'type'       => 'Zend\Form\Element\Select',
            'attributes' => [
                'id'    => 'gender',
                'class' => 'form-control',
            ],
            'options'    => [
                'value_options' => [
                    'male'   => 'Sig.',
                    'female' => 'Sig.ra'
                ]
            ]
        ]);

        $this->add([
            'name'       => 'name',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'       => 'name',
                'class'    => 'form-control',
                'required' => 'required'
            ]
        ]);

        $this->add([
            'name'       => 'surname',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'       => 'surname',
                'class'    => 'form-control',
                'required' => 'required'
            ]
        ]);

        $this->add([
            'name'       => 'email',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'       => 'email',
                'class'    => 'form-control',
                'required' => 'required'
            ]
        ]);

        $this->add([
            'name'       => 'birthDate',
            'type'       => 'Zend\Form\Element\Date',
            'attributes' => [
                'id'       => 'birthDate',
                'class'    => 'form-control date-picker',
                'max'      => date_create()->format('Y-m-d'),
                'required' => 'required',
                'type'     => 'text'
            ]
        ]);

        $this->add([
            'name'       => 'birthCountry',
            'type'       => 'Zend\Form\Element\Select',
            'attributes' => [
                'id'       => 'birthCountry',
                'class'    => 'form-control',
                'required' => 'required'
            ],
            'options'    => [
                'value_options' => $countriesService->getAllCountries()
            ]
        ]);

        $this->add([
            'name'       => 'birthProvince',
            'type'       => 'Zend\Form\Element\Select',
            'attributes' => [
                'id'       => 'birthProvince',
                'class'    => 'form-control',
                'required' => 'required'

            ],
            'options' => [
                'value_options' => $provincesService->getAllProvinces()
            ]
        ]);

        $this->add([
            'name'       => 'birthTown',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'        => 'birthTown',
                'maxlength' => 32,
                'class'     => 'form-control',
                'required'  => 'required'
            ]
        ]);

        $this->add([
            'name'       => 'address',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'        => 'address',
                'maxlength' => 64,
                'class'     => 'form-control',
                'required'  => 'required'
            ]
        ]);

        $this->add([
            'name'       => 'addressInfo',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'        => 'addressInfo',
                'maxlength' => 64,
                'class'     => 'form-control'
            ]
        ]);

        $this->add([
            'name'       => 'zipCode',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'        => 'zipCode',
                'maxlength' => 12,
                'class'     => 'form-control',
                'required'  => 'required'
            ]
        ]);

        $this->add([
            'name'       => 'town',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'        => 'town',
                'maxlength' => 16,
                'class'     => 'form-control',
                'required'  => 'required'
            ]
        ]);

        $this->add([
            'name'       => 'language',
            'type'       => 'Zend\Form\Element\Select',
            'attributes' => [
                'id'    => 'language',
                'class' => 'form-control',
            ],
            'options'    => [
                'value_options' => [
                    "it" => "Italiano",
                    "de" => "tedesco",
                    "fr" => "francese",
                    "es" => "spagnolo",
                    "en" => "inglese",
                    "ch" => "cinese",
                    "ru" => "russo",
                    "pt" => "portoghese"
                ]
            ]
        ]);

        $this->add([
            'name'       => 'taxCode',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'          => 'taxCode',
                'maxlength'   => 16,
                'placeholder' => 'XXXXXXXXXXXXXXXX',
                'class'       => 'form-control',
                'required' => 'required'
            ]
        ]);

        $this->add([
            'name'       => 'vat',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'          => 'vat',
                'maxlength'   => 13,
                'placeholder' => 'ITNNNNNNNNNNN',
                'class'       => 'form-control',
            ]
        ]);

        $this->add([
            'name'       => 'mobile',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'        => 'mobile',
                'maxlength' => 13,
                'class'     => 'form-control',
                'required' => 'required'
            ]
        ]);

        $this->add([
            'name'       => 'phone',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'        => 'phone',
                'maxlength' => 13,
                'class'     => 'form-control',
            ]
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'name'    => [
                'required' => true,
                'filters'  => [
                    [
                        'name' => 'StringTrim'
                    ]
                ]
            ],
            'surname' => [
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
                            'service' => $this->customersService,
                            'avoid' => [
                                $this->customersService->getValidatorEmail()
                            ]
                        ]
                    ]
                ]
            ],
            'birthDate' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'Application\Form\Validator\EighteenDate'
                    ]
                ]
            ],
            'birthTown' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ]
            ],
            'birthCountry' => [
                'required' => true
            ],
            'birthProvince' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ]
            ],
            'address' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ]
            ],
            'zipCode' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ]
            ],
            'town' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ]
            ],
            'taxCode' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ],
                'validators' => [
                    [
                        'name' => 'Application\Form\Validator\DuplicateTaxCode',
                        'options' => [
                            'customerService' => $this->customersService,
                            'avoid' => [
                                $this->customersService->getValidatorTaxCode()
                            ]
                        ]
                    ]
                ]
            ],
            'vat' => [
                'required' => false,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ],
                'validators' => [
                    [
                        'name' => 'Application\Form\Validator\VatNumber'
                    ]
                ]
            ],
            'mobile' => [
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
                            'min' => 3
                        ]
                    ]
                ]
            ],
            'phone' => [
                'required' => false,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 3
                        ]
                    ]
                ]
            ]
        ];
    }

}
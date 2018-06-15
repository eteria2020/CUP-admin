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
 * Class DriverFieldset
 * @package Application\Form
 */
class DriverFieldset extends Fieldset implements InputFilterProviderInterface
{
    /**
     * @param string $name
     * @param array  $options
     */
    public function __construct(
        CountriesService $countriesService,
        AuthorityService $authorityService,
        HydratorInterface $hydrator
    ) {

        parent::__construct('driver', [
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
            'name'       => 'driverLicense',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'    => 'driverLicense',
                'class' => 'form-control',
                'required' => 'required'
            ]
        ]);

        $this->add([
            'name'       => 'driverLicenseName',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'    => 'driverLicenseName',
                'class' => 'form-control',
                'required' => 'required'
            ]
        ]);

        $this->add([
            'name'       => 'driverLicenseSurname',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'    => 'driverLicenseSurname',
                'class' => 'form-control',
                'required' => 'required'
            ]
        ]);

        $this->add([
            'name'       => 'driverLicenseCountry',
            'type'       => 'Zend\Form\Element\Select',
            'attributes' => [
                'id'    => 'driverLicenseCountry',
                'class' => 'form-control',
                'required' => 'required'
            ],
            'options'    => [
                'value_options' => $countriesService->getAllCountries()
            ]
        ]);

        $this->add([
            'name'       => 'driverLicenseExpire',
            'type'       => 'Zend\Form\Element\Date',
            'attributes' => [
                'id'    => 'driverLicenseExpire',
                'class' => 'form-control date-picker',
                'type'  => 'text',
                'required' => 'required'
            ]
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'driverLicense' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'break_chain_on_failure' => true,
                        'options' => [
                            'min' => 6,
                            'max' => 32
                        ]
                    ]
                ]
            ],
            'driverLicenseName' => [
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
                            'min' => 2,
                            'max' => 32
                        ]
                    ]
                ]
            ],
            'driverLicenseSurname' => [
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
                            'min' => 2,
                            'max' => 32
                        ]
                    ]
                ]
            ],
            'driverLicenseCountry' => [
                'required' => true
            ],
            'driverLicenseExpire' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'Date'
                    ],
                ]
            ]
        ];
    }

}

<?php
namespace Application\Form;

// Internal Modules
use SharengoCore\Entity\Customers;
use SharengoCore\Service\CountriesService;
use SharengoCore\Service\CustomersService;
use SharengoCore\Service\ProvincesService;
use SharengoCore\Service\AuthorityService;

// External Modules
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Mvc\I18n\Translator;
use Zend\Stdlib\Hydrator\HydratorInterface;

/**
 * Class SettingFieldset
 * @package Application\Form
 */
class SettingFieldset extends Fieldset implements InputFilterProviderInterface
{
    /**
     * @param string $name
     * @param array  $options
     */
    public function __construct(HydratorInterface $hydrator, Translator $translator)
    {
        parent::__construct('setting', [
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
            'name'       => 'discountRate',
            'type'       => 'Zend\Form\Element\Number',
            'attributes' => [
                'id'    => 'discountRate',
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'name'       => 'registrationCompleted',
            'type'       => 'Zend\Form\Element\Select',
            'attributes' => [
                'id'    => 'registrationCompleted',
                'class' => 'form-control',
            ],
            'options'    => [
                'value_options' => [
                    0 => $translator->translate("No"),
                    1 => $translator->translate("Si")
                ]
            ]
        ]);

        $this->add([
            'name'       => 'enabled',
            'type'       => 'Zend\Form\Element\Select',
            'attributes' => [
                'id'    => 'enabled',
                'class' => 'form-control',
            ],
            'options'    => [
                'value_options' => [
                    0 => $translator->translate("No"),
                    1 => $translator->translate("Si")
                ]
            ]
        ]);

        $this->add([
            'name'       => 'maintainer',
            'type'       => 'Zend\Form\Element\Select',
            'attributes' => [
                'id'    => 'maintainer',
                'class' => 'form-control',
            ],
            'options'    => [
                'value_options' => [
                    0 => $translator->translate("No"),
                    1 => $translator->translate("Si")
                ]
            ]
        ]);

        $this->add([
            'name'       => 'goldList',
            'type'       => 'Zend\Form\Element\Select',
            'attributes' => [
                'id'    => 'goldList',
                'class' => 'form-control',
            ],
            'options'    => [
                'value_options' => [
                    0 => $translator->translate("No"),
                    1 => $translator->translate("Si")
                ]
            ]
        ]);
        
        $this->add([
            'name'       => 'firstPaymentCompleted',
            'type'       => 'Zend\Form\Element\Select',
            'attributes' => [
                'id'    => 'firstPaymentCompleted',
                'class' => 'form-control',
            ],
            'options'    => [
                'value_options' => [
                    0 => $translator->translate("No"),
                    1 => $translator->translate("Si")
                ]
            ]
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'discountRate' => [
                'required' => true,
                'filters'  => [
                    [
                        'name' => 'Int'
                    ],
                ],
                'validators' => [
                    [
                        'name' => 'Between',
                        'options' => [
                            'min' => 0,
                            'max' => 100,
                        ],
                    ],
                ],
            ],
            'registrationCompleted' => [
                'required' => true
            ],
            'enabled' => [
                'required' => true
            ],
            'maintainer' => [
                'required' => true
            ],
            'goldList' => [
                'required' => true
            ],
            'firstPaymentCompleted' => [
                'required' => true
            ]
        ];
    }
}

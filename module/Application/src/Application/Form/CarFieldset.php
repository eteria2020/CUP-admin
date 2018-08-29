<?php
namespace Application\Form;

use SharengoCore\Entity\Cars;
use SharengoCore\Service\CarsService;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Mvc\I18n\Translator;
use Zend\Stdlib\Hydrator\HydratorInterface;

/**
 * Class CarFieldset
 * @package Application\Form
 */
class CarFieldset extends Fieldset implements InputFilterProviderInterface
{
    private $carsService;

    /**
     * @param string $name
     * @param array  $options
     */
    public function __construct(CarsService $carsService, HydratorInterface $hydrator, Translator $translator)
    {
        $this->carsService = $carsService;
        $this->setHydrator($hydrator);
        $this->setObject(new Cars());

        parent::__construct('car', [
            'use_as_base_fieldset' => true
        ]);

        $this->add([
            'name'       => 'plate',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'       => 'plate',
                'class'    => 'form-control',
                'required' => 'required'
            ]
        ]);

        $this->add([
            'name'       => 'manufactures',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'       => 'manufactures',
                'class'    => 'form-control',
                'required' => 'required'
            ]
        ]);

        $this->add([
            'name'       => 'model',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'       => 'model',
                'class'    => 'form-control',
                'required' => 'required'
            ]
        ]);

        $this->add([
            'name'       => 'label',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'       => 'label',
                'class'    => 'form-control',
                'required' => 'required'
            ]
        ]);

        $this->add([
            'name'       => 'notes',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'       => 'notes',
                'class'    => 'form-control'
            ]
        ]);

        $this->add([
            'name'       => 'vin',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'       => 'vin',
                'class'    => 'form-control'
            ]
        ]);

        $this->add([
            'name'       => 'active',
            'type'       => 'Zend\Form\Element\Select',
            'attributes' => [
                'id'    => 'active',
                'class' => 'form-control',
            ],
            'options'    => [
                'value_options' => [
                    1 => $translator->translate("Attivo"),
                    0 => $translator->translate("Non Attivo")
                ]
            ]
        ]);

        $this->add([
            'name'       => 'status',
            'type'       => 'Zend\Form\Element\Select',
            'attributes' => [
                'id'    => 'active',
                'class' => 'form-control js-status',
            ]
        ]);

        $this->add([
            'name'       => 'fleet',
            'type'       => 'Zend\Form\Element\Select',
            'attributes' => [
                'id'    => 'active',
                'class' => 'form-control',
            ]
        ]);
        
        $this->add([
            'name'       => 'location',
            'type'       => 'Zend\Form\Element\Select',
            'attributes' => [
                'id'    => 'active',
                'class' => 'form-control',
            ]
        ]);

    }

    public function getInputFilterSpecification()
    {
        return [
            'plate'        => [
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
            'manufactures' => [
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
            'model'        => [
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
            'label'        => [
                'required' => true,
                'filters'  => [
                    [
                        'name' => 'StringTrim'
                    ]
                ],
                'validators' => [
                    [
                        'name' =>'NotEmpty'
                    ]
                ],
            ],
            'notes'        => [
                'required' => false,
                'filters'  => [
                    [
                        'name' => 'StringTrim'
                    ]
                ]
            ],
            'active'        => [
                'required' => true,
                'filters'  => [
                    [
                        'name' => 'StringTrim'
                    ]
                ]
            ],
            'status'        => [
                'required' => true,
                'filters'  => [
                    [
                        'name' => 'StringTrim'
                    ]
                ]
            ]
        ];
    }

}

<?php
namespace Application\Form;

use SharengoCore\Entity\Cars;
use SharengoCore\Service\CarsService;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
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
    public function __construct(CarsService $carsService, HydratorInterface $hydrator)
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
            'name'       => 'active',
            'type'       => 'Zend\Form\Element\Select',
            'attributes' => [
                'id'    => 'active',
                'class' => 'form-control',
            ],
            'options'    => [
                'value_options' => [
                    1 => "Attivo",
                    0 => "Non Attivo"
                ]
            ]
        ]);

        $this->add([
            'name'       => 'status',
            'type'       => 'Zend\Form\Element\Select',
            'attributes' => [
                'id'    => 'active',
                'class' => 'form-control',
            ],
            'options'    => [
                'value_options' => [
                    "operative" => "Operativa",
                    ['attributes'=>['data-key'=>'out_of_order'],'value'=>'out_of_order', 'disabled'=>'true', 'label'=>'Non operativa'],
                    "maintenance" => "Manutenzione",
                ]
            ]
        ]);

        $this->add([
            'name'       => 'busy',
            'type'       => 'Zend\Form\Element\Select',
            'attributes' => [
                'id'    => 'busy',
                'class' => 'form-control',
            ],
            'options'    => [
                'value_options' => [
                    0 => "Non Occupata",
                    1 => "Occupata"
                ]
            ]
        ]);

        $this->add([
            'name'       => 'hidden',
            'type'       => 'Zend\Form\Element\Select',
            'attributes' => [
                'id'    => 'busy',
                'class' => 'form-control',
            ],
            'options'    => [
                'value_options' => [
                    0 => "Non Nascosta",
                    1 => "Nascosta"
                ]
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
            ],
            'busy'        => [
                'required' => true,
                'filters'  => [
                    [
                        'name' => 'StringTrim'
                    ]
                ]
            ],
            'hidden'        => [
                'required' => true,
                'filters'  => [
                    [
                        'name' => 'StringTrim'
                    ]
                ]
            ],
        ];
    }

}
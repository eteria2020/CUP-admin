<?php
namespace Application\Form;

use SharengoCore\Entity\Pois;
use SharengoCore\Service\PoisService;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;

/**
 * Class PoiFieldset
 * @package Application\Form
 */
class PoiFieldset extends Fieldset implements InputFilterProviderInterface
{
    private $poisService;

    /**
     * @param PoisService $poisService
     * @param HydratorInterface $hydrator
     */
    public function __construct(PoisService $poisService, HydratorInterface $hydrator)
    {
        $this->poisService = $poisService;

        $this->setHydrator($hydrator);
        $this->setObject(new Pois());

        parent::__construct('poi', [
            'use_as_base_fieldset' => true
        ]);

        $this->add([
            'name'       => 'id',
            'type'       => 'Zend\Form\Element\Hidden',
            'attributes' => [
                'id' => 'id'
            ]
        ]);

        $this->add([
            'name'       => 'type',
            'type'       => 'Zend\Form\Element\Select',
            'attributes' => [
                'id'    => 'type',
                'class' => 'form-control',
            ],
            'options'    => [
                'value_options' => [
                    "Colonnine A2A" => "Colonnine A2A",
                    "Isole Digitali" => "Isole Digitali",
                    "n.d." => "n.d.",
                    "Garage" => "Garage",
                    "GGF/CIVES" => "GGF/CIVES",
                    "ACI" => "ACI",
                    "GGF" => "GGF",
                    "GFF" => "GFF",
                ]
            ]
        ]);

        $this->add([
            'name'       => 'code',
            'type'       => 'Zend\Form\Element\Number',
            'attributes' => [
                'id'       => 'code',
                'class'    => 'form-control',
                'required' => 'required'
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
            'name'       => 'address',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'       => 'address',
                'class'    => 'form-control',
                'required' => 'required'
            ]
        ]);

        $this->add([
            'name'       => 'town',
            'type'       => 'Zend\Form\Element\Select',
            'attributes' => [
                'id'    => 'town',
                'class' => 'form-control',
            ],
            'options'    => [
                'value_options' => [
                ]
            ]
        ]);

        $this->add([
            'name'       => 'zipCode',
            'type'       => 'Zend\Form\Element\Number',
            'attributes' => [
                'id'       => 'zipCode',
                'class'    => 'form-control'
            ]
        ]);

        $this->add([
            'name'       => 'province',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'       => 'province',
                'class'    => 'form-control'
            ]
        ]);

        $this->add([
            'name'       => 'lat',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'       => 'lat',
                'class'    => 'form-control'
            ]
        ]);

        $this->add([
            'name'       => 'lon',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'       => 'lon',
                'class'    => 'form-control'
            ]
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'type'        => [
                'required' => true,
            ],
            'code' => [
                'required' => true,
            ],
            'name'        => [
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
            'address'        => [
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
            'town'        => [
                'required' => true,
            ],
            'zipCode' => [
                'required' => true,
            ],
            'province'        => [
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
            'lat'        => [
                'required' => false,
                'filters'  => [
                    [
                        'name' => 'StringTrim'
                    ],
                    [
                        'name' => 'ToNull'
                    ]
                ],
                'validators' => [
                    [
                        'name' =>'IsFloat'
                    ]
                ],
            ],
            'lon'        => [
                'required' => false,
                'filters'  => [
                    [
                        'name' => 'StringTrim'
                    ],
                    [
                        'name' => 'ToNull'
                    ]
                ],
                'validators' => [
                    [
                        'name' =>'IsFloat'
                    ]
                ],
            ],
        ];
    }
}

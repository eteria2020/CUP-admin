<?php

namespace Application\Form;

// Internals
use SharengoCore\Entity\CarsConfigurations;
use SharengoCore\Service\FleetService;
// Externals
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Mvc\I18n\Translator;
use Zend\Stdlib\Hydrator\HydratorInterface;

/**
 * Class CarsConfigurationsFieldset
 * @package Application\Form
 */
class CarsConfigurationsFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(
        Translator $translator,
        FleetService $fleetService,
        HydratorInterface $hydrator
    ) {
        parent::__construct('carsConfigurations', [
            'use_as_base_fieldset' => true
        ]);

        $this->setHydrator($hydrator);
        $this->setObject(new CarsConfigurations());

        $this->add([
            'name' => 'id',
            'type' => 'Zend\Form\Element\Hidden',
            'attributes' => [
                'id' => 'id'
            ]
        ]);

        $this->add([
            'name' => 'fleet',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => [
                'id' => 'fleet',
                'class' => 'form-control',
            ],
            'options' => [
                'value_options' => $fleetService->getFleetsSelectorArray(
                    [ '' => $translator->translate('- Non Specificata -') ]
                )
            ]
        ]);

        $this->add([
            'name' => 'model',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'model',
                'class' => 'form-control',
                'placeholder' => $translator->translate('Modello Auto'),
            ]
        ]);

        $this->add([
            'name' => 'plate',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'plate',
                'class' => 'form-control',
                'placeholder' => $translator->translate('Targa Auto'),
            ]
        ]);

        $this->add([
            'name' => 'key',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'key',
                'class' => 'form-control',
                'placeholder' => $translator->translate('Tipo di Configurazione'),
                'required' => 'required'
            ]
        ]);

        $this->add([
            'name' => 'value',
            'type' => 'Zend\Form\Element\Hidden',
            'attributes' => [
                'id' => 'id'
            ],
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'fleet' => [
                'required' => false,
                'filters' => [
                    [
                        'name' => 'ToNull'
                    ],
                ],
            ],
            'model' => [
                'required' => false,
                'filters' => [
                    [
                        'name' => 'ToNull'
                    ],
                ],
            ],
            'plate' => [
                'required' => false,
                'filters' => [
                    [
                        'name' => 'ToNull'
                    ],
                ],
            ],
            'key' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim',
                        'name' => 'Zend\Filter\Word\SeparatorToCamelCase',
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

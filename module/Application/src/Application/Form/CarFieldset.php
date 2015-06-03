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

    }

    public function getInputFilterSpecification()
    {
        return [
            'plate'    => [
                'required' => true,
                'filters'  => [
                    [
                        'name' => 'StringTrim'
                    ]
                ]
            ],
            'manufactures' => [
                'required' => true,
                'filters'  => [
                    [
                        'name' => 'StringTrim'
                    ]
                ]
            ],
            'model' => [
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
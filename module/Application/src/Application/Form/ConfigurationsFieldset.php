<?php
namespace Application\Form;

use SharengoCore\Entity\Configurations;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;

/**
 * Class ConfigurationsFieldset
 * @package Application\Form
 */
class ConfigurationsFieldset extends Fieldset implements InputFilterProviderInterface
{
    /**
     * ConfigurationsFieldset constructor.
     *
     * @param HydratorInterface $hydrator
     */
    public function __construct(HydratorInterface $hydrator)
    {
        parent::__construct('configurations', [
            'use_as_base_fieldset' => true
        ]);

        $this->setHydrator($hydrator);
        $this->setObject(new Configurations());

        $this->add([
            'name' => 'id',
            'type' => 'Zend\Form\Element\Hidden'
        ]);

        $this->add([
            'name'       => 'configKey',
            'type'       => 'Zend\Form\Element\Hidden',
            'attributes' => [
                'id'       => 'configValue',
                'class'    => 'form-control',
                'required' => 'required'
            ]
        ]);

        $this->add([
            'name'       => 'configValue',
            'type'       => 'Zend\Form\Element\Number',
            'attributes' => [
                'id'       => 'configValue',
                'class'    => 'form-control',
                'required' => 'required'
            ]
        ]);
    }

    /**
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return [
            'id'          => [
                'required'   => true,
                'validators' => [
                    [
                        'name' => 'NotEmpty',
                    ]
                ]
            ],
            'configValue' => [
                'required'   => true,
                'validators' => [
                    [
                        'name' => 'NotEmpty'
                    ]
                ],
            ]
        ];
    }
}
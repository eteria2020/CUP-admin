<?php
namespace Application\Form;

use SharengoCore\Entity\Cards;
use SharengoCore\Service\CardsService;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;

/**
 * Class CardFieldset
 * @package Application\Form
 */
class CardFieldset extends Fieldset implements InputFilterProviderInterface
{
    private $cardService;
    /**
     * @param string $name
     * @param array  $options
     */
    public function __construct(HydratorInterface $hydrator, CardsService $cardService)
    {
        $this->setHydrator($hydrator);
        $this->setObject(new Cards());
        $this->cardService = $cardService;

        parent::__construct('card', [
            'use_as_base_fieldset' => true
        ]);

        $this->add([
            'name'       => 'code',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'       => 'code',
                'class'    => 'form-control',
                'required' => 'required',
                'maxlength' => 8
            ]
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'code'        => [
                'required' => true,
                'filters'  => [
                    [
                        'name' => 'StringTrim'
                    ]
                ],
                'validators' => [
                    [
                        'name' =>'NotEmpty',
                    ],
                    [
                        'name' => 'hex'
                    ],
                    [
                        'name' => 'Application\Form\Validator\DuplicateCards',
                        'options' => [
                            'service' => $this->cardService
                        ]
                    ],
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'max' => 8
                        ]
                    ]
                ],
            ]
        ];
    }
}
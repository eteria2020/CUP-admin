<?php
namespace Application\Form;

use Zend\Form\Fieldset;

/**
 * Class ExtraPaymentsFieldset
 * @package Application\Form
 */
class ExtraPaymentsFieldset extends Fieldset
{
    /**
     * @param string $name
     * @param array  $options
     */
    public function __construct()
    {
        parent::__construct('extra', [
            'use_as_base_fieldset' => true
        ]);

        $this->add([
            'name' => 'customerId',
            'type' => 'Zend\Form\Element\Number',
            'attributes' => [
                'id' => 'customerId',
                'class' => 'form-control'
            ]
        ]);

        $this->add([
            'name' => 'paymentType',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => [
                'id'    => 'paymentType',
                'class' => 'form-control',
            ],
            'options' => [
                'value_options' => [
                    'extra' => 'Extra',
                    'penalty' => 'Penale'
                ]
            ]
        ]);

        $this->add([
            'name' => 'reason',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'    => 'reason',
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'name'       => 'amount',
            'type'       => 'Zend\Form\Element\Number',
            'attributes' => [
                'id'    => 'amount',
                'class' => 'form-control',
                'min' => 0,
                'step' => 0.01
            ]
        ]);
    }
}

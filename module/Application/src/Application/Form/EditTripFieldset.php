<?php
namespace Application\Form;

use Zend\Form\Fieldset;

/**
 * Class EditTripFieldset
 * @package Application\Form
 */
class EditTripFieldset extends Fieldset
{
    /**
     * @param string $name
     * @param array  $options
     */
    public function __construct()
    {
        parent::__construct('trip', [
            'use_as_base_fieldset' => true
        ]);

        $this->add([
            'name'       => 'timestampEnd',
            'type'       => 'Zend\Form\Element\Date',
            'attributes' => [
                'id'       => 'timestampEnd',
                'class'    => 'form-control',
                'type'     => 'text'
            ]
        ]);

        $this->add([
            'name' => 'payable',
            'type' => 'Zend\Form\Element\Checkbox',
            'attributes' => [
                'id'    => 'payable',
                'class' => 'form-control'
            ],
            'options' => [
                'use_hidden_element' => true,
                'checked_value' => 'si',
                'unchecked_value' => 'no'
            ]
        ]);
    }
}

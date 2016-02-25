<?php

namespace Application\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Mvc\I18n\Translator;

class TripCostFieldset extends Fieldset
{
    public function __construct(Translator $translator)
    {
        parent::__construct('tripCost', [
            'use_as_base_fieldset' => true
        ]);

        $this->add([
            'name' => 'tripBeginning',
            'type' => 'Zend\Form\Element\DateTime',
            'attributes' => [
                'id' => 'trip-beginning',
                'class' => 'form-control datetime-picker',
                'type' => 'text'
            ]
        ]);

        $this->add([
            'name' => 'tripEnd',
            'type' => 'Zend\Form\Element\DateTime',
            'attributes' => [
                'id' => 'trip-end',
                'class' => 'form-control datetime-picker',
                'type' => 'text'
            ]
        ]);

        $this->add([
            'name' => 'tripLength',
            'type' => 'Zend\Form\Element\Time',
            'attributes' => [
                'id' => 'trip-length',
                'class' => 'form-control time-picker',
                'type' => 'text'
            ]
        ]);

        $this->add([
            'name' => 'tripParkSeconds',
            'type' => 'Zend\Form\Element\Number',
            'attributes' => [
                'id' => 'trip-park-seconds',
                'class' => 'form-control',
                'min' => 0
            ]
        ]);

        $this->add([
            'name' => 'customerGender',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => [
                'id' => 'customer-gender',
                'class' => 'form-control'
            ],
            'options' => [
                'value_options' => [
                    'male' => $translator->translate('Maschio'),
                    'female' => $translator->translate('Femmina')
                ]
            ]
        ]);

        $this->add([
            'name' => 'customerBonus',
            'type' => 'Zend\Form\Element\Number',
            'attributes' => [
                'id' => 'customer-bonus',
                'class' => 'form-control',
                'min' => 0
            ]
        ]);
    }
}

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
            'name' => 'tripParkMinutes',
            'type' => 'Zend\Form\Element\Number',
            'attributes' => [
                'id' => 'trip-park-minutes',
                'class' => 'form-control',
                'min' => 0,
                'value' => 0
            ]
        ]);

        $this->add([
            'name' => 'customerBonus',
            'type' => 'Zend\Form\Element\Number',
            'attributes' => [
                'id' => 'customer-bonus',
                'class' => 'form-control',
                'min' => 0,
                'value' => 0
            ]
        ]);
    }
}

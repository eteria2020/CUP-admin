<?php

namespace Application\Form;

use Zend\Form\Form;

class CarForm extends Form
{
    public function __construct($carFieldset)
    {
        parent::__construct('car');
        $this->setAttribute('method', 'post');

        $this->add($carFieldset);

        $this->add([
            'name'       => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'value' => 'Submit'
            ]
        ]);

        $this->add([
            'name'       => 'location',
            'type'       => 'Zend\Form\Element\Select',
            'attributes' => [
                'id'    => 'location',
                'class' => 'form-control',
            ],
            'options'    => [
                'value_options' => [
                    'garage velasca' => 'Garage Velasca',
                    'garage sant\'ambrogio' => 'Garage Sant\'Ambrogio',
                    'livorno' => 'Livorno',
                    'milano'  => 'Milano',
                    'philcar' => 'Philcar'
                ]
            ]
        ]);

        $this->add([
            'name'       => 'note',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'    => 'note',
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'name'       => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'value' => 'Submit'
            ]
        ]);
    }

    public function setStatus(array $status)
    {
        $this->get('car')->get('status')->setValueOptions($status);
    }
}

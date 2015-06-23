<?php

namespace Application\Form;

use Zend\Form\Form;

class DriverForm extends Form
{
    public function __construct($driverFieldset)
    {
        parent::__construct('driver');
        $this->setAttribute('method', 'post');
        $this->setAttribute('id', 'driverForm');

        $this->add($driverFieldset);

        $this->add([
            'name'       => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'value' => 'Submit'
            ]
        ]);
    }
}
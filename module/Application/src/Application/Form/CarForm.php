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
    }
}

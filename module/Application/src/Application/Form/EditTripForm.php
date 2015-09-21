<?php

namespace Application\Form;

use Zend\Form\Form;

class EditTripForm extends Form
{
    public function __construct($fieldset)
    {
        parent::__construct('trip');
        $this->setAttribute('method', 'post');
        $this->setAttribute('id', 'editTripForm');

        $this->add($fieldset);

        $this->add([
            'name'       => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'value' => 'Submit'
            ]
        ]);
    }
}

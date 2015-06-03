<?php

namespace Application\Form;

use Zend\Form\Form;

class UserForm extends Form
{
    public function __construct($userFieldset)
    {
        parent::__construct('user');
        $this->setAttribute('method', 'post');

        $this->add($userFieldset);

        $this->add([
            'name'       => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'value' => 'Submit'
            ]
        ]);
    }
}
<?php

namespace Application\Form;

use Zend\Form\Form;

class CardForm extends Form
{
    public function __construct($cardFieldset)
    {
        parent::__construct('card');
        $this->setAttribute('method', 'post');

        $this->add($cardFieldset);

        $this->add([
            'name'       => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'value' => 'Submit'
            ]
        ]);
    }
}

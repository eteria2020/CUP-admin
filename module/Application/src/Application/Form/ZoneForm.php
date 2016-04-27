<?php

namespace Application\Form;

use Zend\Form\Form;

class ZoneForm extends Form
{
    public function __construct($zoneFieldset)
    {
        parent::__construct('poi');
        $this->setAttribute('method', 'post');

        $this->add($zoneFieldset);

        $this->add([
            'name'       => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'value' => 'Submit'
            ]
        ]);
    }
}

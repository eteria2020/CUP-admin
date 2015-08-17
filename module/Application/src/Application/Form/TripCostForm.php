<?php

namespace Application\Form;

use Zend\Form\Form;

class TripCostForm extends Form
{
    public function __construct($userFieldset)
    {
        parent::__construct('tripCost');
        $this->setAttribute('id', 'tripCostForm');

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

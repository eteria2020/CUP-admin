<?php

namespace Application\Form;

use Application\Form\EditTripFieldset;
use Zend\Form\Form;

class EditTripForm extends Form
{
    /**
     * @param EditTripFieldset $fieldset
     */
    public function __construct(EditTripFieldset $editTripFieldset)
    {
        parent::__construct('trip');
        $this->setAttribute('id', 'editTripForm');

        $this->add($editTripFieldset);

        $this->add([
            'name'       => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'value' => 'Submit'
            ]
        ]);
    }
}

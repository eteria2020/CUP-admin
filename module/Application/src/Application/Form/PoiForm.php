<?php

namespace Application\Form;

use Zend\Form\Form;

class PoiForm extends Form
{
    public function __construct($poiFieldset)
    {
        parent::__construct('poi');
        $this->setAttribute('method', 'post');

        $this->add($poiFieldset);

        $this->add([
            'name'       => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'value' => 'Submit'
            ]
        ]);

    }

    /**
     *
     * @param array $fleets list of Fleet instances
     */
    public function setFleets(array $fleets)
    {
        $fleetsPlainArray = [];
        foreach ($fleets as $fleet) {
            $fleetsPlainArray[$fleet->getName()] = $fleet->getName();
        }

        $this->get('poi')->get('town')->setValueOptions($fleetsPlainArray);
    }
}

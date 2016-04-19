<?php

namespace Application\Form;

use Zend\Form\Form;

class CarsConfigurationsForm extends Form
{
    public function __construct($userFieldset)
    {
        parent::__construct('carsConfigurations');
        $this->setAttribute('id', 'carsConfigurationsForm');

        $this->add($userFieldset);

        $this->add([
            'name'       => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'value' => 'Submit'
            ]
        ]);
    }
    
    /*
     * @param array $fleets list of Fleet instances
     */
    public function setFleets(array $fleets)
    {
        $fleetsPlainArray = $this->get('carsConfigurations')->get('fleet')->getValueOptions();
        foreach ($fleets as $fleet) {
            $fleetsPlainArray[$fleet->getId()] = $fleet->getName();
        }

        $this->get('carsConfigurations')->get('fleet')->setValueOptions($fleetsPlainArray);
    }
}

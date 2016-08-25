<?php

namespace Application\Form;

// Internlas
use Application\Form\CarsConfigurationsFieldset;
// Externals
use Zend\Form\Form;

class CarsConfigurationsForm extends Form
{
    public function __construct(CarsConfigurationsFieldset $carConfigurationFieldset)
    {
        parent::__construct('carsConfigurations');
        $this->setAttribute('id', 'carsConfigurationsForm');

        $this->add($carConfigurationFieldset);

        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type' => 'submit',
                'value' => 'Submit'
            ]
        ]);
    }
}

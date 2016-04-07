<?php

namespace Application\Form\CarsConfigurations;

use Zend\Form\Form;

/**
 * Class UseExternalGpsForm
 * @package Application\Form
 */
class UseExternalGpsForm extends Form
{
    public function __construct()
    {
        parent::__construct('carConfiguration');
        $this->setAttribute('method', 'post');

        $this->add([
            'name' => 'UseExternalGPS',
            'type' => 'Zend\Form\Element\Radio',
            'attributes' => [
                'id' => 'externalgps',
                'class' => 'form-control',
                'required' => 'required'
            ],
            'options' => [
                'value_options' => [
                    'false' => 'No',
                    'true' => 'Si',
                ],
                'label' => 'Gps Esterno',
            ]
        ]);
    }
}

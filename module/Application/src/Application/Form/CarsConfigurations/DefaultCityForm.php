<?php

namespace Application\Form\CarsConfigurations;

use Zend\Form\Form;

/**
 * Class DefaultCityForm
 * @package Application\Form
 */
class DefaultCityForm extends Form
{
    public function __construct()
    {
        parent::__construct('carConfiguration');
        $this->setAttribute('method', 'post');

        $this->add([
            'name'       => 'DefaultCity',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'       => 'city',
                'class'    => 'form-control',
                'required' => 'required'
            ],
            'options' => [
                'label' => 'Citta\'',
            ],
        ]);
    }
}

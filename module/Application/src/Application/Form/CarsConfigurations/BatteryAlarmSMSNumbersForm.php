<?php

namespace Application\Form\CarsConfigurations;

use Zend\Form\Form;


/**
 * Class BatteryAlarmSMSNumbersForm
 * @package Application\Form
 */
class BatteryAlarmSMSNumbersForm extends Form
{
    public function __construct()
    {
        parent::__construct('carConfiguration');
        $this->setAttribute('method', 'post');

        $this->add([
            'name'       => 'id',
            'type'       => 'Zend\Form\Element\Hidden',
            'attributes' => [
                'id'       => 'id',
            ],
        ]);

        $this->add([
            'name'       => 'number',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'       => 'number',
                'class'    => 'form-control',
                'required' => 'required'
            ],
            'options' => [
                'label' => 'Numero',
            ],
        ]);
    }
}

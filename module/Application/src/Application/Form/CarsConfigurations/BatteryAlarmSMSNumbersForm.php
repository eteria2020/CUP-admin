<?php

namespace Application\Form\CarsConfigurations;

// Externals
use Zend\Form\Form;
use Zend\Mvc\I18n\Translator;

/**
 * Class BatteryAlarmSMSNumbersForm
 * @package Application\Form
 */
class BatteryAlarmSMSNumbersForm extends Form
{
    public function __construct(Translator $translator)
    {
        parent::__construct('carConfiguration');
        $this->setAttribute('method', 'post');

        $this->add([
            'name' => 'id',
            'type' => 'Zend\Form\Element\Hidden',
            'attributes' => [
                'id' => 'id',
            ],
        ]);

        $this->add([
            'name' => 'number',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'number',
                'class' => 'form-control',
                'required' => 'required'
            ],
            'options' => [
                'label' => $translator->translate('Numero'),
            ],
        ]);
    }
}

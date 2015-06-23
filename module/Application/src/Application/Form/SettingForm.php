<?php

namespace Application\Form;

use Zend\Form\Form;

class SettingForm extends Form
{
    public function __construct($settingFieldset)
    {
        parent::__construct('setting');
        $this->setAttribute('method', 'post');
        $this->setAttribute('id', 'settingForm');

        $this->add($settingFieldset);

        $this->add([
            'name'       => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'value' => 'Submit'
            ]
        ]);
    }
}
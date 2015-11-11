<?php

namespace Application\Form\InputFilter;

use Zend\InputFilter\InputFilter;

class CloseTripFilter extends InputFilter
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->add([
            'name' => 'id',
            'required' => true
        ]);
        $this->add([
            'name' => 'datetime',
            'required' => true,
            'validators' => [
                [
                    
                ]
            ]
        ]);
    }
}

<?php

namespace Application\Form;

use Zend\Form\Form;

class FaresForm extends Form
{
    public function __construct($fieldset)
    {
        parent::__construct('fares');
        $this->setAttribute('method', 'post');
        $this->setAttribute('id', 'faresForm');

        $this->add($fieldset);
    }
}

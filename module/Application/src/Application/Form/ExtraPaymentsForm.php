<?php

namespace Application\Form;

use Zend\Form\Form;

class ExtraPaymentsForm extends Form
{
    public function __construct($fieldset)
    {
        parent::__construct('extra');
        $this->setAttribute('method', 'post');
        $this->setAttribute('id', 'extraForm');

        $this->add($fieldset);
    }
}

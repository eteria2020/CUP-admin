<?php

namespace Application\Form;

use Zend\Form\Form;

class CustomerPointForm extends Form
{
    public function __construct(CustomerPointFieldset $customerPointFieldset)
    {
        parent::__construct('customer-point');
        $this->setAttribute('method', 'post');

        $this->add($customerPointFieldset);
    }
}

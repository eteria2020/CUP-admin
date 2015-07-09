<?php

namespace Application\Form;

use Zend\Form\Form;

class CustomerBonusForm extends Form
{
    public function __construct(CustomerBonusFieldset $customerBonusFieldset) {

        parent::__construct('customer-bonus');
        $this->setAttribute('method', 'post');

        $this->add($customerBonusFieldset);
    }
}

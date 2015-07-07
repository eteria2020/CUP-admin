<?php

namespace Application\Form;

use Zend\Form\Form;

class PromoCodeForm extends Form
{
    public function __construct(PromoCodeFieldset $promoCodeFieldset) {

        parent::__construct('promo-form');
        $this->setAttribute('method', 'post');

        $this->add($promoCodeFieldset);
    }
}

<?php

namespace Application\Form;

use SharengoCore\Service\PromoCodesService;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class PromoCodeFieldset extends Fieldset implements InputFilterProviderInterface
{
    private $promoCodesService;

    public function __construct(PromoCodesService $promoCodesService) {
        $this->promoCodesService = $promoCodesService;

        parent::__construct('promocode', [
            'use_as_base_fieldset' => false
        ]);

        $this->add([
            'name' => 'promocode',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'name',
                'maxlength' => 5,
                'placeholder' => 'Promo code',
            ]
        ]);

    }

    public function getInputFilterSpecification()
    {
        return [
            'promocode' => [
                'required' => false,
                'validators' => [
                    [
                        'name' => 'SharengoCore\Form\Validator\PromoCode',
                        'options' => [
                            'promoCodesService' => $this->promoCodesService
                        ]
                    ]
                ]
            ],
        ];
    }
}

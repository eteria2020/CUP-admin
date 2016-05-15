<?php

namespace Application\Form\CarsConfigurations;

// Externals
use Zend\Form\Form;
use Zend\Mvc\I18n\Translator;

/**
 * Class DefaultCityForm
 * @package Application\Form
 */
class DefaultCityForm extends Form
{
    public function __construct(Translator $translator)
    {
        parent::__construct('carConfiguration');
        $this->setAttribute('method', 'post');

        $this->add([
            'name'       => 'DefaultCity',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'       => 'city',
                'class'    => 'form-control',
                'required' => 'required'
            ],
            'options' => [
                'label' => $translator->translate('Citta\''),
            ],
        ]);
    }
}

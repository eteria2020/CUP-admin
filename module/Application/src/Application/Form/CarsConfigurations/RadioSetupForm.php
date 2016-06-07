<?php

namespace Application\Form\CarsConfigurations;

// Externals
use Zend\Form\Form;
use Zend\Mvc\I18n\Translator;

/**
 * Class RadioSetupForm
 * @package Application\Form
 */
class RadioSetupForm extends Form
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
            'name' => 'volume',
            'type' => 'Zend\Form\Element\Number',
            'attributes' => [
                'id' => 'volume',
                'class' => 'form-control',
                'required' => 'required',
                'min' => '0',
                'max' => '10',
                'step' => '1',
            ],
            'options' => [
                'label' => $translator->translate('Volume'),
            ],
        ]);

        $this->add([
            'name' => 'name',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'name',
                'class' => 'form-control',
                'required' => 'required'
            ],
            'options' => [
                'label' => $translator->translate('Nome'),
            ],
        ]);

        $this->add([
            'name' => 'frequency',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'frequency',
                'class' => 'form-control',
                'required' => 'required'
            ],
            'options' => [
                'label' => $translator->translate('Frequenza'),
            ],
        ]);

        $this->add([
            'name' => 'band',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'band',
                'class' => 'form-control',
                'required' => 'required',
            ],
            'options' => [
                'label' => $translator->translate('Banda'),
            ],
        ]);
    }
}

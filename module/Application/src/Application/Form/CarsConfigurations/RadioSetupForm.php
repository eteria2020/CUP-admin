<?php

namespace Application\Form\CarsConfigurations;

use Zend\Form\Form;

/**
 * Class RadioSetupForm
 * @package Application\Form
 */
class RadioSetupForm extends Form
{
    public function __construct()
    {
        parent::__construct('carConfiguration');
        $this->setAttribute('method', 'post');

        $this->add([
            'name'       => 'id',
            'type'       => 'Zend\Form\Element\Hidden',
            'attributes' => [
                'id'       => 'id',
            ],
        ]);

        $this->add([
            'name' => 'volume',
            'type' => 'Zend\Form\Element\Number',    //@todo specifica range
            'attributes' => [
                'id' => 'volume',
                'class' => 'form-control',
                'required' => 'required',
            ],
            'options' => [
                'label' => 'Volume',
            ],
        ]);

        $this->add([
            'name'       => 'name',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'       => 'name',
                'class'    => 'form-control',
                'required' => 'required'
            ],
            'options' => [
                'label' => 'Nome',
            ],
        ]);

        $this->add([
            'name'       => 'frequency',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'       => 'frequency',
                'class'    => 'form-control',
                'required' => 'required'
            ],
            'options' => [
                'label' => 'Frequenza',
            ],
        ]);

        $this->add([
            'name'       => 'band',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'       => 'band',
                'class'    => 'form-control',
                'required' => 'required',
            ],
            'options' => [
                'label' => 'Banda',
            ],
        ]);
    }
}

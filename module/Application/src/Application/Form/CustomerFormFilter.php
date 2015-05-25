<?php
namespace Application\Form;

use Zend\InputFilter\InputFilter;

/**
 * Class CustomerFormFilter
 * @package Application\Form
 */
class CustomerFormFilter extends InputFilter
{
    public function __construct()
    {
        $this->add([
            'name' => 'name',
            'required' => true,
            'filters' => [
                [
                    'name' => 'StringTrim'
                ]
            ]
        ]);

        $this->add([
            'name' => 'surname',
            'required' => true,
            'filters' => [
                [
                    'name' => 'StringTrim'
                ]
            ]
        ]);

        /*
        $this->add([
            'name' => 'birthDate',
            'required' => true,
            'validators' => [
                [
                    'name' => 'Application\Form\Validator\EighteenDate'
                ]
            ]
        ]);*/
    }
}
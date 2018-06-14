<?php
namespace Application\Form;

use SharengoCore\Service\FaresService;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Class FaresFieldset
 * @package Application\Form
 */
class FaresFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(FaresService $faresService)
    {
        parent::__construct('fares', [
            'use_as_base_fieldset' => true
        ]);

        $fare = $faresService->getFare();
        $costSteps = $fare->getCostSteps();

        $this->add([
            'name' => 'costStep60',
            'type' => 'Zend\Form\Element\Number',
            'attributes' => [
                'id'    => 'costStep60',
                'class' => 'form-control',
                'value' => $costSteps['60']
            ]
        ]);

        $this->add([
            'name' => 'costStep1440',
            'type' => 'Zend\Form\Element\Number',
            'attributes' => [
                'id'    => 'costStep60',
                'class' => 'form-control',
                'value' => $costSteps['1440']
            ]
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'costStep60' => [
                'required' => true,
            ],
            'costStep240' => [
                'required' => true,
            ],
            'costStep1440' => [
                'required' => true,
            ],
        ];
    }
}

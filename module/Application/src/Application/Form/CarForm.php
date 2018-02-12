<?php

namespace Application\Form;

use Zend\Form\Form;
use SharengoCore\Service\MaintenanceMotivationsService;

class CarForm extends Form
{
    private $maintenanceMotivationsService;

    public function __construct($carFieldset, MaintenanceMotivationsService $maintenanceMotivationsService)
    {
        parent::__construct('car');
        $this->setAttribute('method', 'post');
        $this->maintenanceMotivationsService = $maintenanceMotivationsService;

        $this->add($carFieldset);

        $this->add([
            'name'       => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'value' => 'Submit'
            ]
        ]);

        $this->add([
            'name'       => 'location',
            'type'       => 'Zend\Form\Element\Select',
            'attributes' => [
                'id'    => 'location',
                'class' => 'form-control',
            ],
            'options'    => [
                'value_options' => [
                    'garage velasca' => 'Garage Velasca',
                    'garage sant\'ambrogio' => 'Garage Sant\'Ambrogio',
                    'livorno' => 'Livorno',
                    'milano'  => 'Milano',
                    'philcar' => 'Philcar'
                ]
            ]
        ]);

        $this->add([
            'name'       => 'note',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'    => 'note',
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'name'       => 'motivation',
            'type'       => 'Zend\Form\Element\Select',
            'attributes' => [
                'id'    => 'motivation',
                'class' => 'form-control',
            ],
            'options'    => [
                'value_options' =>
                    $maintenanceMotivationsService->getAllMaintenanceMotivations()
            ]
        ]);

        $this->add([
            'name'       => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'value' => 'Submit'
            ]
        ]);

    }

    public function setStatus(array $status)
    {
        $this->get('car')->get('status')->setValueOptions($status);
    }

    /**
     *
     * @param array $fleets list of Fleet instances
     */
    public function setFleets(array $fleets)
    {
        $fleetsPlainArray = [];
        foreach($fleets as $fleet) {
            $fleetsPlainArray[$fleet->getId()] = $fleet->getName();
        }

        $this->get('car')->get('fleet')->setValueOptions($fleetsPlainArray);
    }
    
}

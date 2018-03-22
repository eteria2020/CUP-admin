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
                    ''=>'',
                    'Firenze, Carrozzeria Merciai, via del Pratellino 27/31 50124' => 'Firenze, Carrozzeria Merciai, via del Pratellino 27/31 50124',
                    'Firenze, Rugi, via Gaetano Salvemini 3F, 50058 Signa' => 'Rugi, via Gaetano Salvemini 3F, 50058 Signa',
                    'Firenze, Rugi, via dei Colli 188, 50058 Signa' => 'Rugi, via dei Colli 188, 50058 Signa',
                    'Firenze, Sede, Piazza Eugenio Artom 12, 500127'  => 'Sede, Piazza Eugenio Artom 12, 500127',
                    'Modena, GL Car via Felice Cavallotti n 29 Formigine' => 'Modena, GL Car via Felice Cavallotti n 29 Formigine',
                    'Modena, Carrozzeria Special via Felice Cavallotti Formigine' => 'Modena, Carrozzeria Special via Felice Cavallotti Formigine',
                    'Modena, Carrozzeria Doretto via Viazza II Tronco Ubersetto di Fiorano' => 'Modena, Carrozzeria Doretto via Viazza II Tronco Ubersetto di Fiorano',
                    'Milano, Officina di via Guido da Velate 9 (codice GDV)' => 'Milano, Officina di via Guido da Velate 9 (codice GDV)',
                    'Milano, Carrozzeria GTR Car Service, via Polidoro da Caravaggio (codice GTR)' => 'Milano, Carrozzeria GTR Car Service, via Polidoro da Caravaggio (codice GTR)',
                    'Milano, Deposito carrozzeria GTR Car Service, via Turati, Pero (codice GTR)' => 'Milano, Deposito carrozzeria GTR Car Service, via Turati, Pero (codice GTR)',
                    'Milano, Carrozzeria Midicar, via Ornato (codice MID)' => 'Milano, Carrozzeria Midicar, via Ornato (codice MID)',
                    'Milano, Carrozzeria Idone, via Tiepolo (codice IDO)' => 'Milano, Carrozzeria Idone, via Tiepolo (codice IDO)',
                    'Milano, Carrozzeria Romauto, via Dottesio (codice ROM)' => 'Milano, Carrozzeria Romauto, via Dottesio (codice ROM)',
                    'Milano, Carrozzeria Pennestri, via Portaluppi (codice PEN)' => 'Milano, Carrozzeria Pennestri, via Portaluppi (codice PEN)',
                    'Milano, Carrozzeria DamianiCar, viale Murillo (codice DAM)' => 'Milano, Carrozzeria DamianiCar, viale Murillo (codice DAM)',
                    'Milano, Carrozzeria Brima, via delle Brughiere, Garbagnate Milanese (codice BRI)' => 'Milano, Carrozzeria Brima, via delle Brughiere, Garbagnate Milanese (codice BRI)',
                    'Milano Other (codice OTH)' => 'Milano Other (codice OTH)',
                    'Firenze Other (codice OTH)' => 'Firenze Other (codice OTH)',
                    'Roma Other (codice OTH)' => 'Roma Other (codice OTH)',
                    'Modena Other (codice OTH)' => 'Modena Other (codice OTH)'
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

<?php

namespace Application\Form;

use Zend\Form\Form;
use SharengoCore\Service\MaintenanceMotivationsService;
use SharengoCore\Service\MaintenanceLocationsService;

class CarForm extends Form
{
    private $maintenanceMotivationsService;

    public function __construct($carFieldset, MaintenanceMotivationsService $maintenanceMotivationsService, MaintenanceLocationsService $maintenanceLocationsService)
    {
        parent::__construct('car');
        $this->setAttribute('method', 'post');
        $this->maintenanceMotivationsService = $maintenanceMotivationsService;
        $this->maintenanceLocationsService = $maintenanceLocationsService;

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
                /*'value_options' => [
                    '-'=>'',
                    'Roma, Officina Energeko Srl (Officina interna di riferimento), Via Gregorio VII, 37 - 000165 Roma' => 'Roma, Officina Energeko Srl (Officina interna di riferimento), Via Gregorio VII, 37 - 000165 Roma',
                    'Roma, Carrozzeria Lucarelli, Via della Magliana, 642 – 00148' => 'Roma, Carrozzeria Lucarelli, Via della Magliana, 642 – 00148',
                    'Roma, Carrozzeria Moderna, Via Vecchia di Napoli, 219/223 – 00049 Velletri' => 'Roma, Carrozzeria Moderna, Via Vecchia di Napoli, 219/223 – 00049 Velletri',
                    'Roma, Carrozzeria Lucioli Franco, Via Prospero Intorcetta, 60 - 00126' => 'Roma, Carrozzeria Lucioli Franco, Via Prospero Intorcetta, 60 - 00126',
                    'Roma, Carrozzeria Assistenza car service, Via prenestina nuova km 0.200' => 'Roma, Carrozzeria Assistenza car service, Via prenestina nuova km 0.200',
                    'Roma, Carrozzeria Ventura & Bianchini, SEDE VIA OSTIENSE, 999 00144 - Via Guglielmo Massaia, 17 - Ostiense, 999' => 'Roma, Carrozzeria Ventura & Bianchini, SEDE VIA OSTIENSE, 999 00144 - Via Guglielmo Massaia, 17 - Ostiense, 999',
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
                    
                    $maintenanceLocationsService->getAllMaintenanceLocations(false)
                ]*/
                 'value_options' => $maintenanceLocationsService->getAllMaintenanceLocations(false)
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
            'name'       => 'vin',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'    => 'vin',
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

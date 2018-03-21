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
                    'Carrozzeria Merciai, via del Pratellino 27/31, 50124 Firenze' => 'Carrozzeria Merciai, via del Pratellino 27/31, 50124 Firenze',
                    'Rugi, via Gaetano Salvemini 3F, 50058 Signa' => 'Rugi, via Gaetano Salvemini 3F, 50058 Signa',
                    'Rugi, via dei Colli 188, 50058 Signa' => 'Rugi, via dei Colli 188, 50058 Signa',
                    'Sede, Piazza Eugenio Artom 12, 500127 Firenze'  => 'Sede, Piazza Eugenio Artom 12, 500127 Firenze',
                    'GL Car via Felice Cavallotti n 29 Formigine' => 'GL Car via Felice Cavallotti n 29 Formigine',
                    'Carrozzeria Special via Felice Cavallotti Formigine' => 'Carrozzeria Special via Felice Cavallotti Formigine',
                    'Carrozzeria Doretto via Viazza II Tronco Ubersetto di Fiorano' => 'Carrozzeria Doretto via Viazza II Tronco Ubersetto di Fiorano',
                    'Officina di via Guido da Velate 9, Milano (codice GDV)' => 'Officina di via Guido da Velate 9, Milano (codice GDV)',
                    'Carrozzeria GTR Car Service, via Polidoro da Caravaggio, Milano (codice GTR)' => 'Carrozzeria GTR Car Service, via Polidoro da Caravaggio, Milano (codice GTR)',
                    'Deposito carrozzeria GTR Car Service, via Turati, Pero (codice GTR)' => 'Deposito carrozzeria GTR Car Service, via Turati, Pero (codice GTR)',
                    'Carrozzeria Midicar, via Ornato, Milano (codice MID)' => 'Carrozzeria Midicar, via Ornato, Milano (codice MID)',
                    'Carrozzeria Idone, via Tiepolo, Segrate (codice IDO)' => 'Carrozzeria Idone, via Tiepolo, Segrate (codice IDO)',
                    'Carrozzeria Romauto, via Dottesio (codice ROM)' => 'Carrozzeria Romauto, via Dottesio (codice ROM)',
                    'Carrozzeria Pennestri, via Portaluppi, Milano (codice PEN)' => 'Carrozzeria Pennestri, via Portaluppi, Milano (codice PEN)',
                    'Carrozzeria DamianiCar, viale Murillo, Milano (codice DAM)' => 'Carrozzeria DamianiCar, viale Murillo, Milano (codice DAM)',
                    'Carrozzeria Brima, via delle Brughiere, Garbagnate Milanese (codice BRI)' => 'Carrozzeria Brima, via delle Brughiere, Garbagnate Milanese (codice BRI)',
                    'Other (codice OTH)' => 'Other (codice OTH)'
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

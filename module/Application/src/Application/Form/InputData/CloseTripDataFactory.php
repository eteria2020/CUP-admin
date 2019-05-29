<?php

namespace Application\Form\InputData;

use SharengoCore\Entity\Repository\TripsRepository;
use SharengoCore\Entity\Trips;
use SharengoCore\Exception\InvalidFormInputData;

class CloseTripDataFactory
{
    /**
     * @var TripsRepository $tripsRepository
     */
    private $tripsRepository;

    private $translator;

    public function __construct(TripsRepository $tripsRepository, $translator)
    {
        $this->tripsRepository = $tripsRepository;
        $this->translator = $translator;
    }

    /**
     * creates a new CloseTripData object from an array with keys id, datetime
     * and payable
     *
     * @param array $data
     * @return CloseTripData
     */
    public function createFromArray(array $data)
    {
        $trip = $this->tripsRepository->findOneById($data['id']);

        if (!$trip instanceof Trips) {
            throw new InvalidFormInputData($this->translator->translate('Impossibile trovare la corsa corretta'));
        }

        if (empty($data['datetime'])) {
            throw new InvalidFormInputData($this->translator->translate('Per favore seleziona una data'));
        }

        try {
            $dateTime = date_create($data['datetime']);
        } catch (\Exception $e) {
            throw new InvalidFormInputData($this->translator->translate('La data inserita non è corretta'));
        }

        if ($dateTime > date_create()) {
            throw new InvalidFormInputData($this->translator->translate('Non è possibile utilizzare una data futura'));
        } elseif ($dateTime < $trip->getTimestampBeginning()) {
            throw new InvalidFormInputData($this->translator->translate('Non è possibile chiudere una corsa prima del suo inizio'));
        }

        if ($data['payable'] !== 'yes' && $data['payable'] !== 'no') {
            throw new InvalidFormInputData($this->translator->translate('Per favore seleziona una opzione pagabile'));
        }

        $payable = $data['payable'] === 'yes';

        return new CloseTripData($trip, $dateTime, $payable);
    }
}

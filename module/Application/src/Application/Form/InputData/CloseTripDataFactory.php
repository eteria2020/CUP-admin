<?php

namespace Application\Form\InputData;

use SharengoCore\Entity\Repository\TripsRepository;

class CloseTripDataFactory
{
    /**
     * @var TripsRepository $tripsRepository
     */
    private $tripsRepository;

    public function __construct(TripsRepository $tripsRepository)
    {
        $this->tripsRepository = $tripsRepository;
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
        $dateTime = date_create($data['datetime']);
        $payable = $data['payable'] === 'yes';

        return new CloseTripData($trip, $dateTime, $payable);
    }
}

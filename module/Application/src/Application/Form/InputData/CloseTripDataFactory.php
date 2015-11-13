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

        if (!$trip instanceof Trips) {
            throw new InvalidFormInputData('Impossible to retrieve the correct trip');
        }

        if (empty($data['datetime'])) {
            throw new InvalidFormInputData('Please select a date time');
        }

        try {
            $dateTime = date_create($data['datetime']);
        } catch (\Exception $e) {
            throw new InvalidFormInputData('Impossible to parse the provided datetime');
        }

        if ($dateTime > date_create()) {
            throw new InvalidFormInputData('It is not possible to use a date in the future');
        } else if ($dateTime < $trip->getTimestampBeginning()) {
            throw new InvalidFormInputData('It it not possible to close a trip before its beginning');
        }

        if ($data['payable'] !== 'yes' && $data['payable'] !== 'no') {
            throw new InvalidFormInputData('Please select a payable option');
        }

        $payable = $data['payable'] === 'yes';

        return new CloseTripData($trip, $dateTime, $payable);
    }
}

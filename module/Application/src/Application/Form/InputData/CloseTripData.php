<?php

namespace Application\Form\InputData;

use SharengoCore\Entity\Trips;

class CloseTripData
{
    /**
     * @var Trips $trip
     */
    private $trip;

    /**
     * @var \DateTime $dateTime
     */
    private $dateTime;

    /**
     * @var bool $payable
     */
    private $payable;

    public function __construct(
        Trips $trip,
        \Datetime $dateTime,
        $payable
    ) {
        $this->trip = $trip;
        $this->dateTime = $dateTime;
        $this->payable = $payable;
    }

    /**
     * @return Trips
     */
    public function trip()
    {
        return $this->trip;
    }

    /**
     * @return \DateTime
     */
    public function dateTime()
    {
        return $this->dateTime;
    }

    /**
     * @return bool
     */
    public function payable()
    {
        return $this->payable;
    }

    /**
     * @return Cars
     */
    public function car()
    {
        return $this->trip->getCar();
    }
}

<?php

namespace Application\Command;

use SharengoCore\Command\CommandInterface;
use Application\Form\InputData\CloseTripData;

class CloseTrip implements CommandInterface
{
    /**
     * @var CloseTripData $closeTrip
     */
    private $closeTrip;

    public function __construct(CloseTripData $closeTrip)
    {
        $this->closeTrip = $closeTrip;
    }
}

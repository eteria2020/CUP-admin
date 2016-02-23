<?php
namespace Application\Controller;

use SharengoCore\Service\TripsService;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class TripsNotPayedController extends AbstractActionController
{
    /**
     * @var TripsService
     */
    private $tripsService;

    /**
     * @param TripsService $tripsService
     */
    public function __construct(TripsService $tripsService)
    {
        $this->tripsService = $tripsService;
    }

    public function listAction()
    {
        $trips = $this->tripsService->getTripsNotPayedData();

        return new ViewModel(['trips' => $trips]);
    }
}

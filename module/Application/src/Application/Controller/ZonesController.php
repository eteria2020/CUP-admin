<?php
namespace Application\Controller;

use Application\Entity\Webuser;
use Application\Form\UserForm;
use Application\Form\Validator\DuplicateEmail;
use SharengoCore\Service\ZonesService;
use Zend\Form\Form;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;


class ZonesController extends AbstractActionController
{
    /**
     * @var ZoneService
     */
    private $zoneService;

    /**
     */
    public function __construct(ZonesService $zoneService)
    {
        $this->zoneService = $zoneService;
    }

    public function indexAction()
    {
        return new ViewModel();
    }

    public function tripTabAction()
    {
        $view = new ViewModel([
            'list' => $this->zoneService->getListZones()
        ]);
        $view->setTerminal(true);

        return $view;
    }

    public function alarmsTabAction()
    {
        $view = new ViewModel([
            'list' => $this->zoneService->getListZonesAlarms()
        ]);
        $view->setTerminal(true);

        return $view;
    }

    public function groupsTabAction()
    {
        $view = new ViewModel();
        $view->setTerminal(true);

        return $view;
    }

    public function pricesTabAction()
    {
        $view = new ViewModel();
        $view->setTerminal(true);

        return $view;
    }

}

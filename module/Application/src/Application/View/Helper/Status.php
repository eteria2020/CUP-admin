<?php
namespace Application\View\Helper;
use SharengoCore\Utility\StatusCar;
use Zend\View\Helper\AbstractHelper;

/**
 * Class Status
 * @package Application\View\Helper
 */
class Status extends AbstractHelper
{
    /**
     * @param array $awards
     * @param array $params
     */
    function __invoke($status)
    {
        $label = '';
        switch($status){

            case StatusCar::OPERATIVE:
                $label = 'Operativa';
                break;

            case StatusCar::MAINTENANCE:
                $label = 'In manutenzione';
                break;

            case StatusCar::OUT_OF_ORDER:
                $label = 'Non operativa';
                break;
        }

        return sprintf('<span class="badge badge-default">%s</span>', $label);
    }
}
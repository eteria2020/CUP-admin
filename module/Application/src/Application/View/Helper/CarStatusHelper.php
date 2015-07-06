<?php
namespace Application\View\Helper;
use SharengoCore\Utility\CarStatus;
use Zend\View\Helper\AbstractHelper;

/**
 * Class Status
 * @package Application\View\Helper
 */
class CarStatusHelper extends AbstractHelper
{
    /**
     * @param array $awards
     * @param array $params
     */
    function __invoke($status)
    {
        $label = '';
        switch($status){

            case CarStatus::OPERATIVE:
                $label = 'Operativa';
                break;

            case CarStatus::MAINTENANCE:
                $label = 'In manutenzione';
                break;

            case CarStatus::OUT_OF_ORDER:
                $label = 'Non operativa';
                break;
        }

        return sprintf('<span class="badge badge-default">%s</span>', $label);
    }
}
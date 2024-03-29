<?php
namespace Application\View\Helper;

use SharengoCore\Utility\CarStatus as CarStatusUtility;
use Zend\View\Helper\AbstractHelper;

/**
 * Class Status
 * @package Application\View\Helper
 */
class CarStatus extends AbstractHelper
{
    /**
     * @param array $awards
     * @param array $params
     */
    function __invoke($status)
    {
        $label = '';
        switch ($status) {

            case CarStatusUtility::OPERATIVE:
                $label = $this->getView()->translate('Operativa');;
                break;

            case CarStatusUtility::MAINTENANCE:
                $label = $this->getView()->translate('In manutenzione');
                break;

            case CarStatusUtility::OUT_OF_ORDER:
                $label =  $this->getView()->translate('Non operativa');
                break;
        }

        return sprintf('<span class="badge badge-default">%s</span>', $label);
    }
}
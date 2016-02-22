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
        $translator = $this->TranslatorPlugin();
        $label = '';
        switch ($status) {

            case CarStatusUtility::OPERATIVE:
                $label = $translator->translate('Operativa');
                break;

            case CarStatusUtility::MAINTENANCE:
                $label = $translator->translate('In manutenzione');
                break;

            case CarStatusUtility::OUT_OF_ORDER:
                $label = $translator->translate('Non operativa');
                break;
        }

        return sprintf('<span class="badge badge-default">%s</span>', $label);
    }
}
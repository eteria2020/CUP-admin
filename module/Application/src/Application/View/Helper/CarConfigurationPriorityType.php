<?php
namespace Application\View\Helper;

use SharengoCore\Entity\CarsConfigurations;
use Zend\View\Helper\AbstractHelper;

/**
 * Class CarConfigurationPriorityType
 */
class CarConfigurationPriorityType extends AbstractHelper
{
    /**
     * @param string $priorityType
     */
    function __invoke($priorityType)
    {
        $label = '';
        switch ($priorityType) {
            case CarsConfigurations::GLOBAL_TYPE:
                $label = $this->getView()->translate('Configurazione Globale');
                break;

            case CarsConfigurations::FLEET_TYPE:
                $label = $this->getView()->translate('Configurazione di una Città');
                break;

            case CarsConfigurations::CAR_MODEL_TYPE:
                $label = $this->getView()->translate('Configurazione di un Modello di Auto');
                break;

          case CarsConfigurations::CAR_TYPE:
                $label = $this->getView()->translate('Configurazione Specifica di un Auto');
                break;
        }

        return $label;
    }
}
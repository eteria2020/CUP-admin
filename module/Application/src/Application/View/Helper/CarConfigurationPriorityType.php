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
    public function __invoke($priorityType)
    {
        switch ($priorityType) {
            case CarsConfigurations::GLOBAL_TYPE:
                return $this->getView()->translate('Configurazione Globale');
            case CarsConfigurations::FLEET_TYPE:
                return $this->getView()->translate('Configurazione di una Città');
            case CarsConfigurations::CAR_MODEL_TYPE:
                return $this->getView()->translate('Configurazione di un Modello di Auto');
            case CarsConfigurations::CAR_TYPE:
                return $this->getView()->translate('Configurazione Specifica di un Auto');
        }
    }
}

<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class DatatableFiltersHelper extends AbstractHelper
{
    /**
     * @var array
     */
    private $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function __invoke($page)
    {
        $filters = $this->filters[$page];

        $output = '<div class="row">
            <div class="col-md-5">
                <div class="form-inline">
                    <div class="form-group">
                        <select name="column" id="js-column" class="form-control">
                            <option value="select" selected>--'. $this->getView()->translate("Seleziona") .'--</option>';
        foreach ($filters as $key => $value) {
            $output .= '<option value="' . $key .'">' . $this->getView()->translate($value) . '</option>';
        }
        $output .= '</select>
                    </div>
                    <div class="form-group">
                        <input type="text" name="value" value="" class="form-control" id="js-value"
                               placeholder="'. $this->getView()->translate("Filtra...").'">
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="form-inline">
                    <div class="form-group">
                        <label> '. $this->getView()->translate("da").': </label>
                        <input class="form-control form-control-inline input-small date-picker" size="16"
                               type="text" id="js-date-from"
                               placeholder="'. $this->getView()->translate("Data inizio").'">
                    </div>
                    <div class="form-group">
                        <label> '. $this->getView()->translate("a").': </label>
                        <input class="form-control form-control-inline input-small date-picker" size="16"
                               type="text" id="js-date-to"
                               placeholder="'. $this->getView()->translate("Data fine").'">
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn green js-search" id="js-search"><i
                                class="fa fa-search"></i> '. $this->getView()->translate("Cerca").'</button>
                        <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown"><i
                                class="fa fa-angle-down"></i></button>
                        <ul class="dropdown-menu" role="menu">
                            <li>
                                <a href="javascript:void(0);" id="js-clear"><i
                                        class="fa fa-remove"></i> '. $this->getView()->translate("Pulisci Ricerca").'
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>';

        return $output;
    }
}

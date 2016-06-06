<?php

namespace Application\Form\Element;

// Externals
use Zend\Form\Element;

class GeometryTextarea extends Element
{
    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'type' => 'textarea',
    );

    public function getValue(){
        return $this->getJsonValue();
    }

    /**
     * Retrieve the parsed element value
     *
     * @return mixed
     */
    private function getJsonValue()
    {
        if ($this->value instanceof \CrEOF\Spatial\PHP\Types\Geometry\GeometryInterface) {
            return $this->value->toJson();
        }
        return $this->value;
    }
}

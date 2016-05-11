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
        if ($this->value instanceof \CrEOF\Spatial\PHP\Types\Geometry\GeometryInterface ||
            $this->value instanceof \CrEOF\Spatial\PHP\Types\Geometry\Polygon ||
            $this->value instanceof \CrEOF\Spatial\PHP\Types\Geometry\MultiPolygon ||
            $this->value instanceof \CrEOF\Spatial\PHP\Types\Geometry\LineString ||
            $this->value instanceof \CrEOF\Spatial\PHP\Types\Geometry\MultiLineString ||
            $this->value instanceof \CrEOF\Spatial\PHP\Types\Geometry\Point ||
            $this->value instanceof \CrEOF\Spatial\PHP\Types\Geometry\MultiPoint) {
            return $this->value->toJson();         
        }
        return $this->value;
    }
}

<?php
// @author: C.A.D. BONDJE DOUE
// @file: PropertyMapper.php
// @date: 20230120 22:09:22
namespace IGK\Mapping;

/**
* auto generate doc.
* @package IGK\Mapping
*/
class PropertyMapper{
    /**
    * Property: property.
    * @var mixed
    */
    var $property;
    /**
    * Property: default.
    * @var mixed
    */
    var $default;
    /**
    * .ctr
    * @param string $propety
    */
    public function __construct(string $propety){
        $this->property = $propety;
    }
    /**
    * Map.
    * @param mixed $value
    */
    public function map($value){
        if ($value){
            return igk_getv($value, $this->property);
        }
        return $this->default;
    }
    /**
    * Called when an object is used as a function.
    * @param mixed $value
    */
    public function __invoke($value)
    {
        return $this->map($value);
    }
}
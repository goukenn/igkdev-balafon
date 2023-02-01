<?php
// @author: C.A.D. BONDJE DOUE
// @file: PropertyMapper.php
// @date: 20230120 22:09:22
namespace IGK\Mapping;


///<summary></summary>
/**
* 
* @package IGK\Mapping
*/
class PropertyMapper{
    var $property;

    var $default;

    public function __construct(string $propety){
        $this->property = $propety;
    }
    public function map($value){
        if ($value){
            return igk_getv($value, $this->property);
        }
        return $this->default;
    }
    public function __invoke($value)
    {
        return $this->map($value);
    }
}
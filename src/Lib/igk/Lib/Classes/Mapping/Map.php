<?php
namespace IGK\Mapping;

/**
 * represent object mapper
 * @package IGK\Mapping
 */
class Map{

    /**
     * map object
     * @param object $target 
     * @param mixed $source 
     * @return object
     * @throws IGKException 
     */
    public function map(object $target, $source){
        foreach(array_keys((array)$target) as $k){
            $target->$k = igk_getv($source, $k);
        }
        return $target;
    }
}
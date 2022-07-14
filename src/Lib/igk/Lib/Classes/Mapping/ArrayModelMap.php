<?php

// @author: C.A.D. BONDJE DOUE
// @filename: ArrayModelMap.php
// @date: 20220712 10:19:04
// @desc: 

namespace IGK\Mapping;

/**
 * array model to array mapping
 * @package IGK\Mapping
 */
class ArrayModelMap extends SingleMapBase{

    public function map($data){
        if (is_array($data)){
            return array_map(function($a){
                return $a->to_array();
            }, $data);
        }
        if (method_exists($data, "to_array"))
            return $data->to_array();
        return null;
    }
}

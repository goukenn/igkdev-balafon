<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ArrayExtractor.php
// @date: 20220803 13:48:54
// @desc: 


namespace IGK\System;

use IGKException;

/**
 * use in array_map
 * @package IGK\System
 */
class ArrayExtractor{

    var $key;
    public function __construct(string $key)
    {
        $this->key = $key;
    }
    /**
     * map and return a value of key
     * @param mixed $p 
     * @return mixed 
     * @throws IGKException 
     */
    public function map($p){ 
        return igk_getv($p, $this->key);
    }
    public function li($p){ 
        return "<li>".igk_getv($p, $this->key)."</li>";
    }
}
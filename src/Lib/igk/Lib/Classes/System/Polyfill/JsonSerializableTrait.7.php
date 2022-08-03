<?php
// @author: C.A.D. BONDJE DOUE
// @filename: JsonSerializableTrait.7.php
// @date: 20220803 13:48:55
// @desc: 

namespace IGK\System\Polyfill;

/**
 * trait for json serialisation
 */
trait JsonSerializableTrait{

    public function jsonSerialize(){
        return $this->_json_serialize();
    } 
}
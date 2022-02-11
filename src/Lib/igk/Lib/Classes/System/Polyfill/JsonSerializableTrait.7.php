<?php
namespace IGK\System\Polyfill;

/**
 * trait for json serialisation
 */
trait JsonSerializableTrait{

    public function jsonSerialize(){
        return $this->_json_serialize();
    } 
}
<?php

namespace IGK\Models;

use IGKObject;

class ReferenceObj extends IGKObject{
    private $_ref;

    public function __construct($ref){
        $this->_ref = $ref;
    }
    public function getIsNew(){
        return $this->_ref->newValue;
    }
    public function getNextValue(){
        return $this->_ref->clNextValue;
    }

    public function update(){
        $update = $this->_ref->update;
        $update();
    }
    public function getValue(){
        return $this->_ref->value;
    }
}
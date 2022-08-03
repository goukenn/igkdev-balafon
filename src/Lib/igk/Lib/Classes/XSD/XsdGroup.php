<?php
// @author: C.A.D. BONDJE DOUE
// @filename: XsdGroup.php
// @date: 20220803 13:48:54
// @desc: 


namespace IGK\XSD;

class XsdGroup implements IXsdReference{
    var $name;
    var $attributes;
    
    public function getRefType(){
        return "xs:group";
    }
    public function getRef() { 
        return $this->name;
    }

}
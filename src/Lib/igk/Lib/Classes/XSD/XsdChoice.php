<?php
// @author: C.A.D. BONDJE DOUE
// @filename: XsdChoice.php
// @date: 20220803 13:48:54
// @desc: 


namespace IGK\XSD;

class XsdChoice implements IXsdReference{
    var $name;
    var $attributes;
    
    public function getRefType(){
        return "xs:choice";
    }
    public function getRef() { 
        return $this->name;
    }

}
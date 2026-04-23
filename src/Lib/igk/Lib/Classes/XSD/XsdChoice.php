<?php
// @author: C.A.D. BONDJE DOUE
// @filename: XsdChoice.php
// @date: 20220803 13:48:54
// @desc: 
namespace IGK\XSD;

/**
* Xsd choice.
* @package IGK\XSD
*/
class XsdChoice implements IXsdReference{
    /**
    * Name of name.
    * @var mixed
    */
    var $name;
    /**
    * Property: attributes.
    * @var mixed
    */
    var $attributes;
    /**
    * Returns Ref Type.
    */
    public function getRefType(){
        return "xs:choice";
    }
    /**
    * Returns Ref.
    */
    public function getRef() { 
        return $this->name;
    }
}
<?php
// @author: C.A.D. BONDJE DOUE
// @filename: XsdGroup.php
// @date: 20220803 13:48:54
// @desc: 
namespace IGK\XSD;

/**
* auto generate doc.
* @package IGK\XSD
*/
class XsdGroup implements IXsdReference{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $name;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $attributes;

    /**
    * auto generate doc.
    */

    public function getRefType(){
        return "xs:group";
    }

    /**
    * auto generate doc.
    */

    public function getRef() { 
        return $this->name;
    }
}
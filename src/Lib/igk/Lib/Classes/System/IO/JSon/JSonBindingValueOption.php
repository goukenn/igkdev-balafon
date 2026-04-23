<?php
// @author: C.A.D. BONDJE DOUE
// @file: JSonBindingValueOption.php
// @date: 20250128 15:23:19
namespace IGK\System\IO\JSon;

/**
* auto generate doc.
* @package IGK\System\IO\JSon
* @author C.A.D. BONDJE DOUE
*/
class JSonBindingValueOption{
    /**
    * Property: bind reference.
    * @var mixed
    */
    var $bindReference;
    /**
    * Property: property.
    * @var mixed
    */
    var $property;
    /**
    * Property: source.
    * @var mixed
    */
    var $source;
    /**
    * Property: handle.
    * @var mixed
    */
    var $handle;
    /**
     * to resolve relative type according to the source type 
     * @var mixed
     */
    var $resolveTypeListener;
    /**
     * unshift data
     * @param mixed $obj 
     * @param mixed $value 
     * @return void 
     */
    function unshiftData($obj, $value){
        array_unshift($this->bindReference, ['o'=>$obj, 'd'=>$value]);
    }
}
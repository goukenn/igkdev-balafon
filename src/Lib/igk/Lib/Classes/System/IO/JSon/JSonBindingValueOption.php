<?php
// @author: C.A.D. BONDJE DOUE
// @file: JSonBindingValueOption.php
// @date: 20250128 15:23:19
namespace IGK\System\IO\JSon;
/**
* 
* @package IGK\System\IO\JSon
* @author C.A.D. BONDJE DOUE
*/
class JSonBindingValueOption{
    var $bindReference;
    var $property;
    var $source;
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
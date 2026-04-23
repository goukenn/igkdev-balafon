<?php
// @author: C.A.D. BONDJE DOUE
// @file: JSonBindToConverterBase.php
// @date: 20250128 13:20:16
namespace IGK\System\IO\JSon;

/**
* auto generate doc.
* @package IGK\System\IO\JSon
* @author C.A.D. BONDJE DOUE
*/
abstract class JSonBindToConverterBase{
    /**
    * Called when an object is used as a function.
    * @param mixed $value
    * @param null|mixed $options
    */
    public function __invoke($value, $options=null)
    {
        return $this->convert($value, $options);
    }
    /**
     * convert json value to data type
     * @param mixed $value 
     * @return mixed 
     */
    abstract function convert($value, $options=null);
}
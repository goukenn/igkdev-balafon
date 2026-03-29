<?php
// @author: C.A.D. BONDJE DOUE
// @filename: NotInjectableTypeException.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Exceptions;
use IGKException;
/**
* Not injectable type exception.
* @package IGK\System\Exceptions
*/
class NotInjectableTypeException extends IGKException{
    /**
    * .ctr
    * @param mixed $index
    * @param mixed $code
    */
    public function __construct($index, $code = RequestResponseCode::NotFound){
        parent::__construct( sprintf("Parameter not Injectable %s", $index), $code);
    }
}
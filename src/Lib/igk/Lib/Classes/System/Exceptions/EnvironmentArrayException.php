<?php
// @author: C.A.D. BONDJE DOUE
// @filename: EnvironmentArrayException.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Exceptions;
use IGKException;

/**
* auto generate doc.
* @package IGK\System\Exceptions
*/
class EnvironmentArrayException extends IGKException{

    /**
    * .ctr
    * @param mixed $key
    */
    public function __construct($key){
        parent::__construct( sprintf("Environment key %s not an array", $key), 500);
    }
}
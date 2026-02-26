<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ArgumentTypeNotValidException.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Exceptions;
use IGKException;

/**
* auto generate doc.
* @package IGK\System\Exceptions
*/
class ArgumentTypeNotValidException extends IGKException{

    /**
    * .ctr
    * @param mixed $index
    */
    public function __construct($index){
        parent::__construct( sprintf("Parameter not valid %s", $index));
    }
}
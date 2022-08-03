<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ArgumentTypeNotValidException.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Exceptions;

use IGKException;

class ArgumentTypeNotValidException extends IGKException{
    public function __construct($index){
        parent::__construct( sprintf("Parameter not valid %s", $index));
    }
}
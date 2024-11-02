<?php
// @author: C.A.D. BONDJE DOUE
// @filename: NotInjectableTypeException.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Exceptions;

use IGKException;

class NotInjectableTypeException extends IGKException{
    public function __construct($index, $code = RequestResponseCode::NotFound){
        parent::__construct( sprintf("Parameter not Injectable %s", $index), $code);
    }
}
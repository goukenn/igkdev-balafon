<?php
// @author: C.A.D. BONDJE DOUE
// @filename: InvalidXmlReadException.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Html;

use IGKException;

/**
* Represent IGKInvalidXmlReadException class
*/
class InvalidXmlReadException extends IGKException{
    var $offset;
    /**
    * 
    * @param mixed $msg
    * @param mixed $offset the default value is 0
    */
    public function __construct($msg, $offset=0){
        parent::__construct($msg);
        $this->offset=$offset;
    }
}
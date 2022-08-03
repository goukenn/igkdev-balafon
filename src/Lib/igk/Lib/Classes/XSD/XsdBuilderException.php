<?php
// @author: C.A.D. BONDJE DOUE
// @filename: XsdBuilderException.php
// @date: 20220803 13:48:54
// @desc: 


namespace IGK\XSD;
use IGKException;

/** @package IGK\XSD */
class XsdBuilderException extends IGKException{
    public function __construct($msg)
    {
        parent::__construct($msg);
    }
}
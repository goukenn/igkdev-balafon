<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ArgumentNotValidException.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Exceptions;

use IGKException;
use function igk_resources_gets as __;

class ArgumentNotValidException extends IGKException{
    /**
     * 
     * @param string $argname 
     * @return void 
     */
    public function __construct($argname){
        parent::__construct( sprintf(__("Argument not valid %s"), $argname));
    }
}
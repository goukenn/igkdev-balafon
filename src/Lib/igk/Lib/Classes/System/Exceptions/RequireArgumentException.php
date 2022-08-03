<?php
// @author: C.A.D. BONDJE DOUE
// @filename: RequireArgumentException.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Exceptions;

use IGKException;
use Throwable;
use function igk_resources_gets as __;

/** @package  */
class RequireArgumentException extends IGKException{
    public function __construct($expected, $passed, $code=404, 
    ?Throwable $throwabble=null)
    {
        parent::__construct(sprintf(__("Require parameter missing. passing %s required %s"),
        $passed, $expected), $code, $throwabble);        
    }
}
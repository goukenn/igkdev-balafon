<?php
// @author: C.A.D. BONDJE DOUE
// @filename: BindingContextPropertyNotFoundException.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\Exceptions;

use IGKException;
use Throwable;

/**
 * 
 * @package IGK\Exceptions
 */
class BindingContextPropertyNotFoundException extends IGKException{
    public function __construct(string $propertyname,$code=500, ?Throwable $throw=null)
    {
        $msg = __("Property {0} not found", $propertyname);
        parent::__construct($msg, $code ,$throw);
    }
}
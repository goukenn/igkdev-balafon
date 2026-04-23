<?php
// @author: C.A.D. BONDJE DOUE
// @filename: NotImplementException.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Exceptions;
use IGKException;
use function igk_resources_gets as __; 

/**
* Not implement exception
* @package {IGK\System\Exceptions}
*/
class NotImplementException extends IGKException{
    /**
    * .ctr
    * @param ?string $func
    */
    public function __construct($func){
        parent::__construct(__("Not implement [{0}]", $func));
    }
}
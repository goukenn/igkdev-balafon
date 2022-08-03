<?php
// @author: C.A.D. BONDJE DOUE
// @filename: NotImplementException.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Exceptions;

use IGKException;

use function igk_resources_gets as __; 

///<summary>represent a igk not implement exception</summary>
/**
* represent a igk not implement exception
*/
class NotImplementException extends IGKException{
    ///<summary></summary>
    ///<param name="func"></param>
    /**
    * 
    * @param mixed $func
    */
    public function __construct($func){
        parent::__construct(__("Not implement [{0}]", $func));
    }
}
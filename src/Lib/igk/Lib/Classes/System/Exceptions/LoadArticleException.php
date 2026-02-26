<?php
// @author: C.A.D. BONDJE DOUE
// @filename: LoadArticleException.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Exceptions;
use IGKException;

/**
* Load article exception.
* @package IGK\System\Exceptions
*/
class LoadArticleException extends IGKException{

    /**
    * .ctr
    * @param mixed $key
    */
    public function __construct($key){
        $file = igk_environment()->last("FileLoader");
        parent::__construct( sprintf("Load article throw an error %s", $file), 500);
    }
}
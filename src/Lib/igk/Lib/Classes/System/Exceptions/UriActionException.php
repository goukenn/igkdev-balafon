<?php
// @author: C.A.D. BONDJE DOUE
// @filename: UriActionException.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Exceptions;
use IGKException;
/**
* represent uri action exception
*/
class UriActionException extends IGKException{

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_uri;
    /**
    * 
    * @param mixed $msg
    * @param mixed $uri the default value is null
    * @param mixed $code the default value is 0
    */

    public function __construct($msg, $uri=null, $code=0){
        parent::__construct($msg);
        $this->m_uri=$uri;
    }
    /**
    * 
    */

    public function getUri(){
        return $this->m_uri;
    }
}
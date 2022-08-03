<?php
// @author: C.A.D. BONDJE DOUE
// @filename: UriActionException.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Exceptions;

use IGKException;

///<summary>represent uri action exception</summary>
///<remark>raised when can't handle uri. </remark>
/**
* represent uri action exception
*/
class UriActionException extends IGKException{
    private $m_uri;
    ///<summary></summary>
    ///<param name="msg"></param>
    ///<param name="uri" default="null"></param>
    ///<param name="code"></param>
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
    ///<summary></summary>
    /**
    * 
    */
    public function getUri(){
        return $this->m_uri;
    }
}
<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ResourceNotFoundException.php
// @date: 20220803 13:48:56
// @desc: resource not found exception

namespace IGK\System\Exceptions;

use IGKException;

///<summary>resource not found exception</summary>
/**
*  resource not found exception
*/
class ResourceNotFoundException extends IGKException {
    private $m_file;
    ///<summary></summary>
    ///<param name="message"></param>
    ///<param name="file"></param>
    /**
    * 
    * @param mixed $message
    * @param mixed $file
    */
    public function __construct($message, $file, $code=404){
        parent::__construct($message, $code);
        $this->m_file=$file;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getResourceFile(){
        return $this->m_file;
    }
}
<?php


namespace IGK\System\Exceptions;

use IGKException;

///<summary>ResourceNotFoundException</summary>
/**
* Represente IGKResourceNotFoundException class
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
    public function __construct($message, $file, $code=500){
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
<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ResourceNotFoundException.php
// @date: 20220803 13:48:56
// @desc: resource not found exception
namespace IGK\System\Exceptions;
use IGKException;
/**
*  resource not found exception
*/
class ResourceNotFoundException extends IGKException {

    /**
    * Property: file.
    * @var mixed
    */
    private $m_file;
    /**
    * 
    * @param mixed $message
    * @param mixed $file
    */

    public function __construct($message, $file, $code=404){
        parent::__construct($message, $code);
        $this->m_file=$file;
    }
    /**
    * 
    */

    public function getResourceFile(){
        return $this->m_file;
    }
}
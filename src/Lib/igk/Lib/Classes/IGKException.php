<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKException.php
// @date: 20220803 13:48:54
// @desc: 
  
///<summary>represent a base IGK Framework exception</summary>
/**
* represent a base IGK Framework exception
*/
class IGKException extends \Exception implements Throwable{
   
    ///<summary></summary>
    ///<param name="msg"></param>
    ///<param name="status" default="500"></param>
    /**
    * 
    * @param mixed $msg
    * @param mixed $code the default value is 500
    */
    public function __construct($msg, $code=500, ?\Throwable $throwable=null){
        
        parent::__construct($msg, $code, $throwable);        
    }
    ///<summary>display value</summary>
    /**
    * display value
    */
    public function __toString(){
        return get_class($this);
    }
    ///<summary></summary>
    ///<param name="level" default="1"></param>
    /**
    * 
    * @param mixed $level the default value is 1
    */
    public static function GetCallingFunction($level=1){
        $e=new Exception();
        $trace=$e->getTrace();
        $last_call=$trace[$level];
        return (object)$last_call;
    }
}
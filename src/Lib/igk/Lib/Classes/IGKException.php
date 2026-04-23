<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKException.php
// @date: 20220803 13:48:54
// @desc: 

/**
* represent a base IGK Framework exception
*/
class IGKException extends \Exception implements Throwable{
    /**
    * auto generate doc.
    * @param mixed $code the default value is 500
    */
    public function __construct($msg, $code=500, ?\Throwable $throwable=null){
        parent::__construct($msg, $code, $throwable);        
    }
    /**
    * display value
    */
    public function __toString(){
        return get_class($this);
    }
    /**
    * auto generate doc.
    * @param mixed $level the default value is 1
    */
    public static function GetCallingFunction($level=1){
        $e=new Exception();
        $trace=$e->getTrace();
        $last_call=$trace[$level];
        return (object)$last_call;
    }
}
<?php
// @file: IGKListener.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com

/**
* auto generate doc.
*/
class IGKListener implements IListener{
    private $listener;

    /**
    * auto generate doc.
    * @param mixed $n
    * @param mixed $args
    */
    public function __call($n, $args){
        $f=igk_getv($this->listener, $n);
        if(is_callable($f)){
            return \call_user_func_array($f, $args);
        }
    }

    /**
    * auto generate doc.
    * @param mixed $n
    * @param mixed $args
    */
    public static function __callStatic($n, $args){
        die("dieNotAllowed");
    }

    /**
    * auto generate doc.
    * @param mixed $n
    * @param mixed $callback
    */
    public function Register($n, $callback){
        $this->listener[$n]=$callback;
    }
}
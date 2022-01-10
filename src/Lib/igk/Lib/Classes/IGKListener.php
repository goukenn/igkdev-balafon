<?php
// @file: IGKListener.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

class IGKListener implements IIGKListener{
    private $listener;
    ///<summary>Represente __call function</summary>
    ///<param name="n"></param>
    ///<param name="args"></param>
    public function __call($n, $args){
        $f=igk_getv($this->listener, $n);
        if(is_callable($f)){
            return \call_user_func_array($f, $args);
        }
    }
    ///<summary>Represente __callStatic function</summary>
    ///<param name="n"></param>
    ///<param name="args"></param>
    public static function __callStatic($n, $args){
        die("dieNotAllowed");
    }
    ///<summary>Represente Register function</summary>
    ///<param name="n"></param>
    ///<param name="callback"></param>
    public function Register($n, $callback){
        $this->listener[$n]=$callback;
    }
}

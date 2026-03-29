<?php
// @file: IGKBalafonFrameworkManager.php
// @author: C.A.D. BONDJE DOUE
// @description:
// @copyright: igkdev © 2020
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\Manager;
/**
* Framework manager
*/
class BalafonFrameworkManager{
    /**
    * Property: handle all action.
    * @var mixed
    */
    var $handleAllAction;
    /**
    * auto generate doc.
    * @param mixed $args
    */
    public function __call($name, $args){
        $f="igk_".$name;
        if(function_exists($f)){
            igk_wl(call_user_func_array($f, $args));
        }
        else{
            echo "command [{$name}] not found";
        }
    }
    /**
    * auto generate doc.
    */
    public function __construct(){
        $this->handleAllAction=1;
    }
    /**
    * auto generate doc.
    */
    public function clear_cache(){
        igk_clear_cache();
    }
    /**
    * echo help message
    */
    public function help(){
        echo "help ";
    }
    /**
    * auto generate doc.
    */
    public function install(){
        echo "running install";
    }
    /**
    * auto generate doc.
    */
    public function test(){
        echo "run test";
    }
}
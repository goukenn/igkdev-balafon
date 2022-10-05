<?php
// @author: C.A.D. BONDJE DOUE
// @file: HookHandler.php
// @date: 20220905 10:05:13
namespace IGK\System;


///<summary>hook handler</summary>
/**
* hook handler
* @package IGK\System
*/
class HookHandler{
    private $callable;
    private $args;
    public function __construct($callable, ...$args)
    {
        $this->callable = $callable;
        $this->args = $args;
    }
    public function invoke($e){
        $bck= $e->args;
        $e->args = array_merge($e->args, $this->args ?? []);        
        $r = call_user_func_array($this->callable, [$e]);
        $e->args = $bck;
        return $r;
    }
}
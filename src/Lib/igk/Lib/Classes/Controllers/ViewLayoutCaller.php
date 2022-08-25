<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ViewLayoutCaller.php
// @date: 20220814 09:19:43
// @desc: 


namespace IGK\Controllers;
/**
 * help invoke layout method in view context
 * @package 
 */
class ViewLayoutCaller{
    var $arguments;
    var $name;
    var $host;

    public function invoke($node){
        $args = array_merge(func_get_args(), $this->arguments ?? []);
        return call_user_func_array([$this->host, $this->name], $args);
    }
}
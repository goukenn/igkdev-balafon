<?php
// @author: C.A.D. BONDJE DOUE
// @file: JSonOption.php
// @date: 20230404 13:07:37
namespace IGK\Helper;


/**
* 
* @package IGK\Helper
*/
class JSonEncodeOption{
    var $ignore_empty = false;
    
    var $ignore_null = false;
    
    var $filter_array_listener;

    var $allow_key_assoc_empty_array;
    public static function IgnoreEmpty(){
        $s = new static;
        $s->ignore_empty = true;
        $s->ignore_null = true;
        return $s;
    }
}
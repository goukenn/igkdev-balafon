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

    /**
    * auto generate doc.
    * @var mixed
    */
    var $ignore_empty = false;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $ignore_null = false;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $filter_array_listener;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $allow_key_assoc_empty_array;

    /**
    * auto generate doc.
    */
    public static function IgnoreEmpty(){
        $s = new static;
        $s->ignore_empty = true;
        $s->ignore_null = true;
        return $s;
    }
}
<?php
// @author: C.A.D. BONDJE DOUE
// @file: JSonOption.php
// @date: 20230404 13:07:37
namespace IGK\Helper;

/**
* auto generate doc.
* @package IGK\Helper
*/
class JSonEncodeOption{

    /**
    * Property: ignore empty.
    * @var mixed
    */
    var $ignore_empty = false;

    /**
    * Property: ignore null.
    * @var mixed
    */
    var $ignore_null = false;

    /**
    * Listener: filter array listener.
    * @var mixed
    */
    var $filter_array_listener;

    /**
    * Collection of allow key assoc empty array.
    * @var mixed
    */
    var $allow_key_assoc_empty_array;

    /**
    * Ignore empty.
    */
    public static function IgnoreEmpty(){
        $s = new static;
        $s->ignore_empty = true;
        $s->ignore_null = true;
        return $s;
    }
}
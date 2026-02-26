<?php
// @author: C.A.D. BONDJE DOUE
// @file: ReadTokenFlagOptions.php
// @date: 20221023 14:52:30
namespace IGK\System\Runtime\Compiler;
/**
* 
* @package IGK\System\Runtime\Compiler
*/
class ReadTokenFlagOptions{

    /**
    * Property: buffer.
    * @var mixed
    */
    var $buffer = "";

    /**
    * Creates Flag.
    * @param null|array $tab
    */
    public static function CreateFlag(?array $tab=null){
        $c = new static;
        if ($tab){
            foreach(get_class_vars(static::class) as $k=>$v){               
                $c->$k = igk_getv($tab, $k, $v);
            }
        }
        return $c;
    }
}
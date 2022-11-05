<?php
// @author: C.A.D. BONDJE DOUE
// @file: ReadTokenFlagOptions.php
// @date: 20221023 14:52:30
namespace IGK\System\Runtime\Compiler;


///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler
*/
class ReadTokenFlagOptions{
    var $buffer = "";
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
<?php
// @author: C.A.D. BONDJE DOUE
// @file: SessionValueOperator.php
// @date: 20221010 01:44:07
namespace IGK\System\Http\Session;


///<summary></summary>
/**
* 
* @package IGK\System\Http\Session
*/
class SessionValueOperator{
    public static function __callStatic($name, $arguments)
    {
        $cl = "\\".__NAMESPACE__."\\SessionOperator".ucfirst($name)."Callback";
        if (class_exists($cl)){
            $g = new $cl(...$arguments);
            $c = \Closure::fromCallable([$g, 'invoke']);
            return $c;
        } 
        return null;
    }
}
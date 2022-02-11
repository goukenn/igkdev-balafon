<?php
// file: ActionHelper.php
// author: C.A.D BONDJE DOUE
// date: 2022-10-02
// description: contains function that IGKActionBase can use

namespace IGK\Helper;

use ReflectionMethod;

/**
 * action helper
 * @package IGK\Helper
 */
abstract class ActionHelper{
    /**
     * sanitize method name
     * @param string $name 
     * @return string 
     */
    public static function SanitizeMethodName(?string $name){
        if ($name===null){
            return $name;
        }
        $name = trim(preg_replace("/[^0-9\w]/", "_", $name));
        return $name;
    }
    /**
     * bind request args
     * @param mixed $object 
     * @param mixed $action 
     * @param mixed $args 
     * @return void 
     */
    public static function BindRequestArgs($object, $action, & $args){
        $g = new ReflectionMethod($object, $action); 
        if (($g->getNumberOfRequiredParameters() == 1) && ($cl = $g->getParameters()[0]->getType()) && igk_is_request_type($cl)) {
            $req = \IGK\System\Http\Request::getInstance();
            $req->setParam($args);
            $args = [$req];
        } 
    }
}
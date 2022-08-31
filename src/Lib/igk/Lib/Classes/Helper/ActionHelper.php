<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ActionHelper.php
// @date: 20220803 13:48:57
// @desc: 

// @file: ActionHelper.php
// @author: C.A.D BONDJE DOUE
// date: 2022-10-02
// description: contains function that IGKActionBase can use

namespace IGK\Helper;

use ReflectionMethod;

/**
 * action helper
 * @package IGK\Helper
 */
abstract class ActionHelper{
    //do nothing
    /**
     * used to pass empty anonymous
     * @return callable 
     */
    public static function Nothing(): callable{
        return function(){
            // nothing call back method
        };
    }
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
    /**
     * handle args helper 
     * @param string $fname 
     * @param array $handleArgs 
     * @return bool 
     */
    public static function HandleArgs(string $fname , array & $handlerArgs, string $entryName=IGK_DEFAULT):bool{        
        if ((strpos($fname, "/") !== false) && !igk_str_endwith($fname, $entryName)) {
            if (!empty($tb = array_slice(explode("/", $fname), -1)[0])) {
                array_unshift($handlerArgs, $tb);
            }
            return true;
        }
        return false;
    }
}
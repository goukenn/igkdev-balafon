<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Utility.php
// @date: 20220803 13:48:58
// @desc: utility helper
namespace IGK\Helper;
use Exception; 
use IGK\System\Http\RequestUtility;
use stdClass;

/**
* Utility.
* @package IGK\Helper
*/
abstract class Utility {

    /**
    * Post cref.
    * @param callable $callback
    * @param mixed $valid
    * @param mixed $method
    */
    public static function PostCref(callable $callback, $valid=1, $method="POST"){
        if (igk_server()->method($method) && igk_valid_cref($valid)){
            return $callback();
        }
        return false;
    }

    /**
    * auto generate doc.
    * @param bool $update
    * @return mixed
    */
    public static function RequestGet($paramHandler, $requestName, $paramName, $update=true){
       return RequestUtility::RequestGet(...func_get_args());
    }
    /**
     * get the email display
     * @param mixed $r 
     * @return string 
     */
    public static function GetUserEmailDisplay($r){
        return implode(" ",array_filter([
            strtoupper($r->clLastName), ucfirst($r->clFirstName),
            "&lt;".$r->clLogin."&gt;"]));
    }
    /**
     * get the user fullname display
     * @param mixed $r 
     * @return string 
     */
    public static function GetFullName($r){
        return implode(" ", array_filter([ strtoupper($r->clLastName), ucfirst($r->clFirstName)]));
    }
    /**
     * helper: convert raw to json.
     * @param mixed $raw 
     * @param mixed|null $options , ignore_empty=1|0 , default_ouput='{}'
     * @return mixed 
     * @throws Exception 
     */
    public static function To_JSON($raw , $options=null, $json_option = JSON_UNESCAPED_SLASHES){
        return JSon::Encode($raw, $options, $json_option);       
    }

    /**
    * auto generate doc.
    * @param string $clasname
    */
    public static function GetStaticClassMethods(string $clasname){
        $tab = array_map(function($i)use($clasname){
            return $clasname."::".$i; 
         }, get_class_methods($clasname));        
        sort($tab);
        return $tab;
    }
}
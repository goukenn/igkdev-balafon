<?php
// @author: C.A.D. BONDJE DOUE
// @filename: sys.php
// @date: 20230323 12:53:54
// @desc: system helper function 

use IGK\System\Text\RegexMatcherContainer;

if (!function_exists('igk_sys_request_time')){

/**
* Igk sys request time.
*/
function igk_sys_request_time(){
        $time = $_SERVER["REQUEST_TIME_FLOAT"];
        return (microtime(true) - $time);
    }
}

if (!function_exists('igk_sys_reflect_filter_public_properties')) {

    /**
     * filter only relection properties
     * @param array $o 
     * @param ReflectionClass $ref 
     * @return void 
     */
    function igk_sys_reflect_filter_public_properties(array $o, ReflectionClass $ref)
    {
        $o = array_filter(array_map(function ($a, $key) use ($ref) {
            if (($p = $ref->getProperty($key)) && $p->isPublic()) {
                return $a;
            }
            return null;
        }, $o, array_keys($o)));
    }
}

if (!function_exists('igk_sys_get_html_components')){

/**
* Igk sys get html components.
* @param null|string $pattern
*/
function igk_sys_get_html_components(?string $pattern=null){

        $g = array_filter(array_map(function ($g) use($pattern) {
            if (preg_match("/^" . IGK_FUNC_NODE_PREFIX . "(?P<name>.+)/", $g, $tab) && (!is_string($pattern) || preg_match($pattern, $tab[1]) )) {
                return substr($g, strlen(IGK_FUNC_NODE_PREFIX));
            }
        }, get_defined_functions()['user']));
        sort($g);
        return $g;
    }
}

if (!function_exists('igk_sys_reflect_is_support_trait')){
    /**
     * the ReflectionClass::getTraits method return only the trait attached to current class 
     * need to throw parent 
     * @var string $class_name
     * @var string $trait_class
     */
    function igk_sys_reflect_is_support_trait(string $class_name , string $trait_class){
        if ($r = igk_sys_reflect_class($class_name)){
            while($r && !in_array($trait_class, array_keys($r->getTraits()))){
                $r = $r->getParentClass();            
            }
            return $r != null;
        }
        return false;
    }
}


if (!function_exists('igk_sys_cookies_read_value')) {
    /**
     * read cookies values string
     * @param string $data 
     * @return array<string,string>
     * @throws IGKException 
     * @throws Exception 
     */
    function igk_sys_cookies_read_value(string $data){ 
        return igk_regex_read_value($data, '=',';');
    }
}

if (!function_exists('igk_sys_cookies_build')){

/**
* Igk sys cookies build.
* @param array $cookies_entries
*/
function igk_sys_cookies_build(array $cookies_entries){
        return implode(";", array_map(function($a,$b){ return $b.'='.$a; }, $cookies_entries, array_keys($cookies_entries)));
    }
}

// use function \preg_last_error_msg;
if (!function_exists('preg_last_error_msg')){

/**
* Preg last error msg.
*/
function preg_last_error_msg(){ 

        if ($c = preg_last_error()){
            return 'preg_last_error: '.$c;
        }
        return $c;
    }
}
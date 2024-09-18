<?php
// @author: C.A.D. BONDJE DOUE
// @filename: sys.php
// @date: 20230323 12:53:54
// @desc: system helper function 

if (!function_exists('igk_sys_request_time')){
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

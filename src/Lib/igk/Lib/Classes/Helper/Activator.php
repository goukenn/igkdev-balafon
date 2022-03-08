<?php
namespace IGK\Helper;
/**
 * 
 * @package IGK\Helper;
 */
class Activator{
    /**
     * create class instance. \
     *      class must context a public constructor \
     *      data pass to it will be used to initialize public properties
     * 
     * @param mixed $classame 
     * @param mixed $data 
     * @return object 
     * @throws IGKException 
     * @throws Exception class not found
     */
    public static function CreateNewInstance($classame, $data = null){
        $g = new $classame();
        if ($data){
            foreach(get_class_vars($classame) as $k=>$v){
                $g->{$k} = igk_getv($data, $k) ?? $v;
            }
        }
        return $g;
    }
}
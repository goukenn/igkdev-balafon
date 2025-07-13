<?php
// @author: C.A.D. BONDJE DOUE
// @file: Facade.php
// @date: 20221005 14:19:33
namespace IGK\System\Facades;
use IGKException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use ReflectionException;
use Exception;
/**
* facade creator
* @package IGK\Systems\Facades
*/
class Facade{    
    private static function _GetCoreClass($f){
        $core_ns = IGK_CORE_ENTRY_NS."/";
        if (strpos($f, $core_ns) === 0){
            $f = substr($f, 4);
        }
        return $f;
    }
    /**
     * retrieve base class facade
     * @param mixed $baseclass 
     * @param null|string $primaryClass 
     * @return mixed|void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws Exception 
     */
    public static function GetFacade(string $baseclass, ?string $primaryClass=null){
        // + | 
        // + |  try to load class and facade 
        if (class_exists($baseclass, false)){
            return $baseclass;
        }
        // + | 
        // + | lauchn class fafcase 
        $facades = & igk_environment()->createArray(__CLASS__);
        $cl = self::_GetCoreClass(igk_uri($baseclass));
        $g = IGK_LIB_CLASSES_DIR."/".$cl.".php";
        $refclass = $primaryClass ?? $baseclass; 
        try{
            require_once($g);
            if (!class_exists($refclass, false)){
                igk_environment()->isDev() && igk_die("failed to resolve facade : ".$baseclass);
                return null;
            }
            $facades[$baseclass] = $g;
            return $baseclass;
        } catch (Exception $ex){
            igk_wln_e("error loading facade ". $g);
        }
    }
}
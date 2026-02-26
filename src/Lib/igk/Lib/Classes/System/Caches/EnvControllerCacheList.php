<?php
// @author: C.A.D. BONDJE DOUE
// @filename: EnvControllerCacheList.php
// @date: 20221031 10:22:32
// @desc: environment controller cache list
namespace IGK\System\Caches;
use IGKEvents;
use ReflectionClass;
// + | --------------------------------------------------------------------
// + | ENV : Controller Cache list : so we can easely retrieve detected 
// + | loaded controller 
// + |
/**
 * 
 * @package IGK\System\Caches
 */
class EnvControllerCacheList{
   //  use CachableDataTrait;

    /**
    * Constant: file.
    * @var mixed
    */
    const FILE = ".env.controller.cache";

    /**
    * Cache: cachelist.
    * @var mixed
    */
    private static $sm_cachelist;

    /**
    * Property: changed.
    * @var mixed
    */
    private static $sm_changed;

    /**
    * Returns Cache File.
    */
    public static function GetCacheFile(){
        return igk_io_cachedir().'/'. self::FILE;
    }
    /**
     * get declared class list
     * @return mixed 
     */

    public static function GetControllersClasses(){ 
        if (is_null(self::$sm_cachelist)){ 
            $tab = false;
            if (is_file($file = self::GetCacheFile())){
                $tab = unserialize(file_get_contents($file));
            }
            if ($tab === false){
                // load declared class that is a base controller and not module 
                $tab = array_values(array_filter(get_declared_classes(), function($a){
                    if (is_subclass_of($a, \IGK\Controllers\BaseController::class)){
                        $refClass = new ReflectionClass($a);
                        if ($refClass->isAbstract() || igk_sys_is_path_in_module($refClass->getFileName())){
                            return null;
                        } 
                        return $a;
                    }
                })); 
                // $cl = 'igk\pay\paypal\Component\paypalpaymentCtrl';
                // $c = new ReflectionClass($cl);
                // igk_wln_e(__FILE__.":".__LINE__ , $tab, realpath(igk_get_module_dir()), $c->getFileName());
                self::$sm_changed = true;           
            }
            igk_reg_hook(IGKEvents::HOOK_APP_SHUTDOWN, function(){
                if (self::$sm_changed){
                     igk_io_w2file(self::GetCacheFile(), serialize(self::$sm_cachelist));
                     self::$sm_changed = false;
                }
            });
            igk_reg_hook(IGKEvents::HOOK_CONTROLER_LOADED, function($e){
                $c = $e->args['ctrl'];
                if (!in_array($cl = get_class($c), self::$sm_cachelist) && !igk_sys_is_module_controller($c)){
                    self::$sm_cachelist[] = $cl;
                    self::$sm_changed = true;
                }
            });
            self::$sm_cachelist = & $tab;
        }
        return self::$sm_cachelist;
    }
}
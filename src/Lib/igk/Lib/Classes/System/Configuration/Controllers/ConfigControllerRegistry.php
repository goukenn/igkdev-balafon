<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ConfigControllerRegistry.php
// @date: 20220731 11:10:12
// @desc: 

namespace IGK\System\Configuration\Controllers;

use Exception;

use IGKEvents;
use IGK\Controllers\IRegisterOnInitController;
use IGK\Manager\ApplicationControllerManager;
use IGK\System\Diagnostics\Benchmark;
use IGKException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use ReflectionException;

/**
 * config controller registry
 * @package IGK\System\Configuration\Controllers
 */
class ConfigControllerRegistry
{
    const LOADED_CONFIG_CTRL = "config_controllers";
    private static $sm_regComplete;



    ///<summary>RegisterInitComplete . if Ctrl is not null add it to base controller list</summary>
    ///<param name="ctrl">if null return the count number of the registrated controller. else register the controller to iniList</param>
    /**
     * RegisterInitComplete . if Ctrl is not null add it to base controller list
     * @param mixed $ctrl if null return the count number of the registrated controller. else register the controller to iniList
     */
    public static function RegisterInitComplete($ctrl = null)
    {
        if (self::$sm_regComplete === null)
            self::$sm_regComplete = array();
        $register = $ctrl && ($ctrl instanceof IRegisterOnInitController); // in_array(IRegisterOnInitController::class,  class_implements($ctrl, false));

        if (($ctrl !== null) && (!$register || $ctrl->getCanRegisterOnInit())) {
            self::$sm_regComplete[] = $ctrl;
        }
        return igk_count(self::$sm_regComplete);
    }
    /**
     * unset controller from index
     * @param mixed $ctrl 
     * @return void 
     */
    public static function UnRegisterInitComplete($ctrl)
    {
        if (self::$sm_regComplete && ($index = array_search($ctrl, self::$sm_regComplete))) {
            unset(self::$sm_regComplete[$index]);
        }
    }


    ///<summary></summary>
    /**
     * 
     */
    public static function InvokeRegisterComplete($context = null)
    {
        if (self::$sm_regComplete){
            $cnf = igk_environment()->getControllerInfo();
            $cnf->initCount = count(self::$sm_regComplete); 
  
            foreach (self::$sm_regComplete as  $v){
                $_cl = get_class($v); 
                Benchmark::mark(get_class() . "::initComplete");
                $v->initComplete($context);
                Benchmark::expect(
                    get_class() . "::initComplete",
                    0.1,
                    sprintf("%s took too long", $_cl)
                );
                $cnf->ctrls[] = $_cl;
            } 
        }
        self::$sm_regComplete = null;
    }
    /**
     * register configuration class
     * @param string $class 
     * @return bool 
     * @throws EnvironmentArrayException 
     */
    public static function Register(string $class, $name = null)
    {

        if (is_subclass_of($class, ConfigControllerBase::class)) {
            $key = $name ? $name : $class;
            igk_environment()->setArray(self::LOADED_CONFIG_CTRL, $key, $class);
            return true;
        }
        return false;
    }
    /**
     * Get merged system initial controller list with environment loaded config controller.
     * @return array
     */
    public static function GetResolvController():array
    {
        // merge controller view configuration controllers. 
        $resolv_ctrl = ApplicationControllerManager::GetResolvController();
        if ($jump = igk_environment()->get(self::LOADED_CONFIG_CTRL)) {
            $resolv_ctrl = array_merge($resolv_ctrl, $jump); // array_combine(array_keys($jump), array_values($jump)));
        }
        return $resolv_ctrl;
    }
    /**
     * init controller and resolv controller
     * @return mixed 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException  
     */
    public static function ResolvAndInitControllers()
    {
        //
        // load configuration controller 
        // init all because must be required
        // 
        if ($d = igk_environment()->get($key = "init_resolv_ctrls")){
            return $d;
        }

       //  igk_die(__METHOD__.":: Not implement get configuration controller - Basics");

        $v_load_controller = igk_app()->getControllerManager()->getControllerRef();
        // igk_wln_e($v_load_controller, __FILE__.":".__LINE__, );
        $resolv_ctrl = self::GetResolvController();
        foreach ($resolv_ctrl as $k => $v) {
            if (!isset($v_load_controller[$v])) {
                if ($ctrl = igk_getctrl($k, false)) {
                    $v_load_controller[get_class($ctrl)] = $ctrl;
                }
            }
        }
        igk_hook(IGKEvents::HOOK_CONFIG_CTRL, [
            "loaded" => &$v_load_controller
        ]);
        $v_load_controller = array_unique(array_values($v_load_controller));
        igk_environment()->set($key, $v_load_controller);
        return $v_load_controller;
    }
}

<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKModuleListMigration.php
// @date: 20220803 13:48:54
// @desc: 


use IGK\Controllers\BaseController;
use IGK\Controllers\ControllerExtension;
use IGK\System\Console\Logger;

final class IGKModuleListMigration extends BaseController{
    private static $sm_list;
    private static $sm_instance;
    private $host;
    private function __construct()
    {
        
    }
    public static function Create(array $list){

        self::$sm_list = $list;
        $g = new self();
        return $g;
    }  
    public static function CreateModulesMigration(){
        if ( $modules = igk_get_modules()){
            $list = array_filter( array_map(function($c, $k){
                if ($mod = igk_get_module($k)){
                    return $mod;
                }
            }, $modules, array_keys($modules)));
            return self::Create($list);       
        }
    }
    public static function migrate(){
        Logger::print("migrate modules ");
        self::$sm_instance = new self();
        foreach(self::$sm_list as $l){
            Logger::info("migrate .... ".$l->getName());
            self::$sm_instance->host = $l;
            try{
                ControllerExtension::migrate(self::$sm_instance);
            }
            catch(Exception $ex){
                Logger::danger("error ... ".$ex->getMessage());
                return false;
            }
        }
        return true;
    }
    public static function resetDb($navigate=false, $force=false){
        self::$sm_instance = new self();
        $fc = BaseController::getMacro("resetDb");
        foreach(self::$sm_list as $l){
            Logger::info("reset module db .... ".$l->getName());
            self::$sm_instance->host = $l;
            $fc(self::$sm_instance, $navigate, $force);
            ControllerExtension::migrate(self::$sm_instance);
        } 
        return true;
    }
    public function __call($n, $argument){ 
        if ($this->host){
            return call_user_func_array([$this->host, $n ],  $argument);
        }
        else {
            if (igk_environment()->isDev()){
                igk_trace();
                igk_wln_e("try call :::".$n);
            }
        }
    }
    public static function __callStatic($name, $arguments)
    {
        if (method_exists(ControllerExtension::class, $name)){
            array_unshift($arguments, self::$sm_instance->host);
            return ControllerExtension::$name(...$arguments);
        }
        return null;
    }
    public function getCanInitDb(){
        return true;
    }
    public function register_autoload(){

    }
    
}
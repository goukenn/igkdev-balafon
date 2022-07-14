<?php
namespace IGK\System\Services;

use IGK\Helper\ViewHelper;
use IGK\Models\Injectors\ModelBaseInjector;

/**
 * 
 * @package 
 */
class InjectorProvider{

    private $injectors;

    private static $sm_instance;

    private function __construct() {
        
    }
    /**
     * 
     * @return static 
     */
    public static function getInstance(){
        if (self::$sm_instance === null){
            self::$sm_instance = new self();
        }
        return self::$sm_instance;
    }
    /**
     * 
     * @return mixed 
     */
    public static function GetInjectors(): ?array{
        return self::getInstance()->injectors;
    }
    /**
     * get injector instance from type
     * @param string $type 
     * @return ModelBaseInjector|void 
     */
    public function injector(string $type){
        if (is_subclass_of($type, \IGK\Models\ModelBase::class)){
            return new \IGK\Models\Injectors\ModelBaseInjector($type::model());
        }
        if (is_subclass_of($type, \IGK\Controllers\BaseController::class)){
            return new \IGK\Models\Injectors\ControllerInjector($type::ctrl());
        }
        if ($type == \IGK\Controllers\BaseController::class){
            return new \IGK\Models\Injectors\ControllerInjector(ViewHelper::CurrentCtrl());
        }
    }
}
<?php
// @author: C.A.D. BONDJE DOUE
// @filename: InjectorProvider.php
// @date: 20220803 13:48:55
// @desc: 
namespace IGK\System\Services;
use IGK\Helper\ViewHelper;
use IGK\Models\Injectors\ModelBaseInjector;

/**
* auto generate doc.
*/

/**
* auto generate doc.
* @package IGK\System\Services
*/
class InjectorProvider{

    /**
    * Property: injectors.
    * @var mixed
    */
    private $injectors;

    /**
    * Property: instance.
    * @var mixed
    */
    private static $sm_instance;

    /**
    * .ctr
    * @return
    */
    private function __construct() {
    }

    /**
    * auto generate doc.
    * @return static
    */

    public static function getInstance(){
        if (self::$sm_instance === null){
            self::$sm_instance = new self();
        }
        return self::$sm_instance;
    }

    /**
    * auto generate doc.
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
            if ($c = ViewHelper::CurrentCtrl()){
                return new \IGK\Models\Injectors\ControllerInjector($c); 
            }            
        }
    }
}
<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ControllerInjector.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\Models\Injectors;
use IGK\Controllers\BaseController;
use IGK\Models\ModelBase;
use IGK\System\IInjector;
/**
 * controller injector
 * @package IGK\Models\Injectors
 */
class ControllerInjector implements IInjector{

    /**
    * auto generate doc.
    * @var mixed
    */
    protected $controller;

    /**
    * .ctr
    * @param null|BaseController $controller
    */
    public function __construct(?BaseController $controller=null)
    {
        $this->controller = $controller;
    }
    /**
     * return according to the value
     * @param mixed $value 
     * @param null|string $type 
     * @return mixed 
     */

    public function resolve($value, ?string $type=null){      
        if ($value instanceof BaseController){
            return $value;
        }
        return $this->controller;        
    }

    /**
    * get string presentation.
    */
    public function __toString(){
        return __CLASS__;
    }
}
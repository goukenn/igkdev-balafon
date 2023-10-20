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
    protected $controller;
    public function __construct(BaseController $controller=null)
    {
        $this->controller = $controller;
    }
    public function resolve($value, ?string $type=null){        
        return $this->controller;        
    }
}

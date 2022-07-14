<?php

namespace IGK\Models\Injectors;

use IGK\Controllers\BaseController;
use IGK\Models\ModelBase;

/**
 * controller injector
 * @package IGK\Models\Injectors
 */
class ControllerInjector{
    protected $controller;

    public function __construct(BaseController $controller=null)
    {
        $this->controller = $controller;
    }

    public function resolv($i=null){        
        return $this->controller;        
    }
}

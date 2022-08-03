<?php

// @author: C.A.D. BONDJE DOUE
// @filename: ViewLayoutBase.php
// @date: 20220801 08:23:19
// @desc: base view layout loader

namespace IGK\Controllers;

use IGK\System\WinUI\IViewLayoutLoader;

/**
 * layout base loader
 * @package IGK\Controllers
 */
abstract class ViewLayoutBase implements IViewLayoutLoader {
    /**
     * get the controller
     * @var BaseController
     */
    protected $controller;
    public function getController(): BaseController
    {
        return $this->controller;
    }

    public function __construct(BaseController $controller)
    {
        $this->controller = $controller;
        $this->initialize();
    }
    protected function initialize(){ 
    }
    /**
     * check if file exists
     * @param mixed $file 
     * @return bool 
     */
    protected function exists($file){
        return !empty($file) && file_exists($file);
    }
}

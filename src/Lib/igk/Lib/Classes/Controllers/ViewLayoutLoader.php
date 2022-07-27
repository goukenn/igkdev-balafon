<?php

// @author: C.A.D. BONDJE DOUE
// @filename: ViewLayoutLoader.php
// @date: 20220605 13:04:13
// @desc: view layout loader

namespace IGK\Controllers;

use IGK\Helper\ViewHelper;
use IGK\System\WinUI\IViewLayoutLoader;

use function igk_resources_gets as __;

/**
 * view layout loader
 * @package IGK\Controllers
 */
class ViewLayoutLoader implements IViewLayoutLoader{
    private $m_controller;

    var $header;

    var $footer;

    public function getController(): BaseController
    {
        return $this->m_controller;
    }

    public function __construct(BaseController $controller)
    {
        $this->m_controller = $controller;
        $this->header =  $this ->m_controller->getViewDir()."/.header.pinc";
        $this->footer =  $this->m_controller->getViewDir()."/.footer.pinc";

        if (method_exists($this->m_controller, "menuFilter")){
            igk_reg_hook("filter-menu-item", [$this->m_controller, "menuFilter"]);
        }
    }
    /**
     * check if file exists
     * @param mixed $file 
     * @return bool 
     */
    protected function exists($file){
        return !empty($file) && file_exists($file);
    }
    public function include($file, $args){
        $response = null;   
        $args["doc"]->title =  __("{0} - [{1}]", __("title.{$args['fname']}")  , $this->m_controller->getConfigs()->get('clAppTitle', igk_configs()->website_domain));
        if ($this->exists($this->header)){
            igk_include_view_file($this->m_controller, $this->header, $args);
        }
        $response = igk_include_view_file($this->m_controller, $file, $args);
        if ($this->exists($this->footer)){
            igk_include_view_file($this->m_controller, $this->footer, $args);
        }
        return $response;
    }

}
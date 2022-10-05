<?php

// @author: C.A.D. BONDJE DOUE
// @filename: ViewLayoutLoader.php
// @date: 20220605 13:04:13
// @desc: view layout loader

namespace IGK\Controllers;

use Exception;
use IGK\Helper\ViewHelper;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\WinUI\IViewLayoutLoader;
use IGKEnvironment;
use IGKException;
use ReflectionException;

use function igk_resources_gets as __;

/**
 * view layout loader
 * @package IGK\Controllers
 */
class ViewLayoutLoader extends ViewLayoutBase implements IViewLayoutLoader{
 
    var $header;

    var $footer;   

    const LAYOUT_PAGE_PARAM  = "@PageLayout";

    public function __construct(BaseController $controller)
    {
        parent::__construct($controller);    
    }
    protected function initialize(){
        $this->header =  $this ->controller->getViewDir()."/.header.pinc";
        $this->footer =  $this->controller->getViewDir()."/.footer.pinc";
        if (method_exists($this->controller, "menuFilter")){
            igk_reg_hook("filter-menu-item", [$this->controller, "menuFilter"]);
        } 
    }
    /**
     * include file in layout
     * @param string $file 
     * @param null|array $args 
     * @return mixed 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws EnvironmentArrayException 
     * @throws Exception 
     */
    public function include($file, $args){
        $response = null; 
        $no_layout = false;
        $this->controller->setExtraArgs(["layout"=>$this]);

        $args["doc"]->title =  __("{0} - [{1}]", __("title.{$args['fname']}")  , $this->controller->getConfigs()->get('clAppTitle', igk_configs()->website_domain));
        if (!$no_layout && $this->exists($this->header)){
            igk_include_view_file($this->controller, $this->header, $args);   
        }
        $response = igk_include_view_file($this->controller, $file, $args);
        $no_layout = $this->Configs[self::LAYOUT_PAGE_PARAM];
        if (!$no_layout && $this->exists($this->footer)){
            igk_include_view_file($this->controller, $this->footer, $args);
        }
        return $response;
    }
    public function getPageTitle(string $title){
        return sprintf("%s - [%s]", $title,  $this->controller->getConfigs()->get('clAppTitle', igk_configs()->website_domain));
    }
    public function loginForm(){
        return function($b){
            $form = igk_create_node("form");
            $form->fields([
                "login"=>["type"=>"text"],
                "pwd"=>["type"=>"password"]
            ]);
            $b->add($form);        
        };
    }
}
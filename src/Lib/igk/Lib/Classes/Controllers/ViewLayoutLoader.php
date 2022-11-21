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
use IGK\System\Html\HtmlRenderer;
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
 
    /**
     * 
     * @var mixed
     */
    var $header;

    /**
     * footer view 
     * @var mixed
     */
    var $footer;   

    /***
     * default title 
     */
    var $title;

    /**
     * const to store page layout param.
     */
    const LAYOUT_PAGE_PARAM  = "@PageLayout";
    /**
     * const to pass parameter beetween each include views.
     */
    const PAGE_PARAM = "@PageParams";
    /**
     * const activate the main layout param
     */
    const MAIN_LAYOUT_PARAM = "@MainLayout";

    public function __construct(BaseController $controller)
    { 
        parent::__construct($controller);    
        $this->MainLayout = "JUMMM3";
    }
    protected function initialize(){
        $this->header =  $this ->controller->getViewDir()."/.header.pinc";
        $this->footer =  $this->controller->getViewDir()."/.footer.pinc";
        if (method_exists($this->controller, "menuFilter")){
            igk_reg_hook("filter-menu-item", [$this->controller, "menuFilter"]);
        } 
    }
    /**
     * interupt inclusion
     * @return void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function interup(){

        HtmlRenderer::RenderDocument(igk_app()->getDoc()); 
        igk_exit();
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
    public function include(string $file, ?array $args = null){
  
        $response = null;
        $ctrl =  $this->controller;  
        $this->controller->setExtraArgs(["layout"=>$this]);
        $v_main = $this->{'@MainLayout'};
        $no_cache = $ctrl->getEnvParam(ControllerEnvParams::NoCompilation) || $ctrl->getConfig('no_auto_cache_view');

        

        $args["doc"]->title =  $this->title  ?? $this->getPageTitle(__("title.{$args['fname']}"));

        if (!$v_main &&  $this->exists($this->header)){
            igk_include_view_file($this->controller, $this->header, true, $args);   
        }
        $response = igk_include_view_file($this->controller, $file, 
        $no_cache,
        $args);        
        if (!$v_main &&  $this->exists($this->footer)){
            igk_include_view_file($this->controller, $this->footer, true, $args);
        }
        return $response;
    }
    /**
     * get page title 
     * @return string
     */
    public function getPageTitle(string $title, $main=false):string{

        return $main ? 
            sprintf("%s ", $title):
            sprintf("%s - [ %s ]", $title, 
            $this->controller->getConfig('clAppTitle', igk_configs()->website_domain));
    }
    /**
     * login form callback
     * @return callable
     */
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
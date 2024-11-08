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
use IGK\System\IO\Path;
use IGK\System\Views\ViewCommentArgs;
use IGK\System\WinUI\IViewLayoutLoader; 
use IGKException;
use ReflectionException;
use function igk_resources_gets as __;

/**
 * view layout loader
 * @package IGK\Controllers
 */
class ViewLayoutLoader extends ViewLayoutBase implements IViewLayoutLoader{

    private $m_dir;
    /**
     * store parameter 
     * @var ?object
     */
    var $m_params;
    /**
     * header view file
     * @var ?string
     */
    var $header;

    /**
     * footer view file 
     * @var ?string
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
    }
    protected function initialize(){
        $this->header =  $this ->controller->getViewDir()."/.header.pinc";
        $this->footer =  $this->controller->getViewDir()."/.footer.pinc";
        if (method_exists($this->controller, "menuFilter")){
            igk_reg_hook("filter-menu-item", [$this->controller, "menuFilter"]);
        } 
    }
    /**
     * get location location 
     * @return void 
     */
    public function dir(){
        return $this->m_dir ?? Path::Combine($this->controller->getDeclaredDir(), "/ViewLayout");
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
     * get object reference params - layout
     * @return mixed 
     */
    public function param(){
        return $this->m_params ?? $this->m_params = igk_createobj();
    }
    /**
     * include view file 
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
        $v_main = $this->isMainLayout($file); 
        $v_no_cache = $ctrl->getEnvParam(ControllerEnvParams::NoCompilation) || $ctrl->getConfigs()->no_auto_cache_view;
        $args["doc"]->title =  $this->title  ?? $this->getPageTitle(__("title.{$args['fname']}"));
        
        $v_header = $this->_resolveContextFile($this->header, dirname($file));
        $v_footer = $this->_resolveContextFile($this->footer, dirname($file));

        
        if (!$v_main &&  $this->exists($v_header)){
            igk_include_view_file($this->controller, $v_header, true, $args);   
        }
        $response = igk_include_view_file($this->controller, $file, $v_no_cache, $args);        
        if (!$v_main &&  $this->exists($v_footer)){
            igk_include_view_file($this->controller, $v_footer, true, $args);
        }
        $this->afterInc();
        return $response;
    }

    private function _resolveContextFile($file, $bdir){
        $g = array_values(array_filter(explode($this->controller->getViewDir(), $file,2)));
        if (file_exists($f = $bdir.$g[0])){
            return $f;
        }
        return $file;
    }

    /**
     * import file  
     * @param string $file 
     * @param null|array *3b22bd6a 
     * @param IGK\Controllers\args|null *2c206736 
     * @return void 
     */
    public function import(string $file, ?array $args = null){
        return ViewHelper::Include($file, $args);
    }
    /**
     * afert view inclusion
     * @return void 
     */
    protected function afterInc(){
        // to some thing after inclusion
    }
    /**
     * check if the view is a main layout 
     * @param string $file 
     * @return bool 
     */
    public function isMainLayout(string $file): bool{
        return $this->{'@MainLayout'} || ViewCommentArgs::Check("@MainLayout()", $file);
    }
    /**
     * get page title 
     * @return string
     */
    public function getPageTitle(string $title, $main=false):string{

        return $main ? 
            sprintf("%s ", $title):
            sprintf("%s - [ %s ]", $title, 
            $this->controller->getConfig(\IGK\System\Configuration\ConfigurationFields::AppTitle, igk_configs()->website_domain));
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
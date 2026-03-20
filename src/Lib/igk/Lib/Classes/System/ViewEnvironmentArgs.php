<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ViewEnvironmentArgs.php
// @date: 20220828 22:35:31
// @desc: 
namespace IGK\System;
use ArrayAccess;
use IGK\Controllers\BaseController;
use IGK\Helper\Activator;
use IGK\Helper\StringUtility;
use IGK\Helper\ViewHelper;
use IGK\System\Controllers\ControllerSysKeyConstants;
use IGK\System\Html\Css\CssUtils;
use IGK\System\Html\HtmlNodeBuilder;
use IGK\System\Http\Request;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
use IGKException; 
/**
 * represent view environment args - shared  accross views definition
 * @package 
 */
class ViewEnvironmentArgs implements ArrayAccess{
    use ArrayAccessSelfTrait;
    /**
     * target node 
     */
    var $t;
    /**
     * get or set the engine builder
     * @var mixed
     */
    var $builder;
    /**
     * current document . set it with 
     * @var mixed
     */
    var $doc;
    /**
     * current controller
     * @var mixed
     */
    var $ctrl;
    /**
     * file entry point
     * @var mixed
     */
    var $file;
    /**
     * list of loaded controller
     * @var mixed
     */
    var $controllers;
    /**
     * store the request instance 
     * @var mixed
     */
    var $request;
    /**
     * view context
     * @var mixed
     */
    var $viewcontext;
    /**
     * func_get_args 
     * @var mixed
     */
    var $func_get_args;
    /**
     * parameter to pass to view
     * @var mixed
     */
    var $params;
    /**
     * stored module list
     * @var mixed
     */
    var $modules;
    /**
     * entry path view
     * @var mixed
     */
    var $fname;
    /**
     * full request uri
     * @var mixed
     */
    var $furi;
    /**
     * full access path
     */
    var $fpath;
    /**
     * base uri without the entry access
     * @var string
     */
    var $base_uri;
    /**
     * root entry path request folder
     * @var string
     */
    var $rname;
    /**
     * execution content
     * @var mixed
     */
    var $context;
    /**
     * store the buffer level
     * @var mixed
     */
    var $ob_level;
    /**
     * query fill options
     * @var mixed
     */
    var $query_options;
    /**
     * boolean to said is a directory entry
     * @var mixed
     */
    var $is_direntry;
    /**
     * store user info
     * @var ?UserInfo
     */
    var $user;
    /**
     * store authenticator
     * @var ?IGK\System\Security\Authenticator 
     */
    var $auth;
    /**
     * full entry uri
     * @var ?string
     */
    var $entry_uri;
    /**
     * base directory 
     * @var ?string
     */
    var $dir;
    /**
     * view layout
     * @var mixed
     */
    var $layout;
    /**
     * data to pass by default to article
     * @var mixed
     */
    var $data;
    /**
     * get or set the action handler class
     * @var mixed
     */
    var $action_handler;
    /**
     * get current action instance
     * @var mixed
     */
    var $action;
    /**
     * session data
     * @var ?IGKSession
     */
    var $session;
    /**
     * expected response code after main view call . will be used at last of document view . default is null = 200
     * @var ?int
     */
    var $responseCode;
    /**
     * initialize with the current view file directory 
     * @var mixed
     */
    var $_dir_;
    /**
     * global css definition 
     * @var ?string
     */
    var $css_def;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $css_m;
    /**
     * current doc theme definition styles
     * @var mixed
     */
    var $def;

    /**
     * global error provided by action call
     * @var mixed
     */
    var $error;

    /**
     * the attached submit domain 
     * @var ?string
     */
    var $subdomain;


    /** 
     * get context view argument  
     * @param BaseController $controller source controller
     * @param string $file to view
     * @param string $context context id 
     * @return static  
     * @throws IGKException 
     */
    public static function CreateContextViewArgument(BaseController $controller, string $file, string $context){
        $fname = igk_io_getviewname($file, $controller->getViewDir());
        $rname = igk_io_view_root_entry_uri($controller, $fname); 
        $params = array_filter($controller->getEnvParam(BaseController::VIEW_ARGS) ?? [], StringUtility::NotNullOrEmptyFilterCallback());
        extract(array_merge(
            $controller->getSystemVars(),
            $controller->utilityViewArgs($fname, $file),
        ), EXTR_SKIP);
        $fpath = igk_getv(parse_url($furi), 'path');
        $controller->setEnvParam('fulluri', $furi);
        $params = isset($params) ? $params : array();
        $query_options = $controller->getEnvParam(IGK_VIEW_OPTIONS);
        $is_direntry = (count($params) == 0) && igk_str_endwith(explode('?', igk_io_request_uri())[0], '/');
        if ($t){
            $controller->bindNodeClass($t, $fname, strtolower((isset($css_def) ? " " . $css_def : "")));
        }
        // $doc->body["class"] = "-custom-thumbnail";
        // $doc->title = igk_configs()->website_title();
        $ob_level = ob_get_level();
        $controller->_get_extra_args($file);
        $session = igk_app()->getSession(); 
        $base_uri = $controller::uri('/');
        $builder = $builder ?? $t ? new HtmlNodeBuilder($t) : null;
        $_dir_ = ViewHelper::Dir() ?? dirname($file); 
        $subdomain = $controller->getEnvParam(ControllerSysKeyConstants::subdomain);
        if ($css_m = CssUtils::GetCssClassName($controller)){
            $css_m = '.'.$css_m;
        }
        $def = $doc->getTheme();
        $request = Request::getInstance();
        $g = Activator::CreateNewInstance(static::class, get_defined_vars());
        return $g; 
    }

    /**
    * check if isset innaccessible property
    * @param mixed $name
    */
    public function __isset($name)
    { 
        return property_exists($this, $name);
    }

    /**
    * get string presentation.
    */
    public function __toString(){
        return __CLASS__;
    }

    /**
    * Access offset get.
    * @param mixed $n
    */
    protected function _access_OffsetGet($n){
        if (property_exists($this,$n)){
            return $this->$n;
        }
    }

    /**
    * Access offset set.
    * @param mixed $n
    * @param mixed $v
    */
    protected function _access_OffsetSet($n, $v){
        if (property_exists($this,$n)){
            $this->$n = $v;
        }
    }

    /**
    * Access offset exists.
    * @param mixed $n
    */
    protected function _access_offsetExists($n){
        return (property_exists($this,$n));
    }
}
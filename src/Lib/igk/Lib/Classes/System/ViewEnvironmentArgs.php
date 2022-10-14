<?php

// @author: C.A.D. BONDJE DOUE
// @filename: ViewEnvironmentArgs.php
// @date: 20220828 22:35:31
// @desc: 


namespace IGK\System;

use IGK\Controllers\BaseController;
use IGK\Helper\Activator;
use IGKException;

/**
 * represent view environment args - shared  accross views definition
 * @package 
 */
class ViewEnvironmentArgs{
    /**
     * target node 
     */
    var $t;

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
     * entry path request uri
     * @var mixed
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
     * define entry uri
     * @var mixed
     */
    var $entry_uri;

    /**
     * base directory 
     * @var mixed
     */
    var $dir;

    /**
     * data to pass by default to article
     * @var mixed
     */
    var $data;

    /**
     * get context view argument
     * @param BaseController $controller 
     * @param string $file 
     * @return static 
     * @throws IGKException 
     */
    public static function GetContextViewArgument(BaseController $controller, string $file, string $context){
        $fname = igk_io_getviewname($file, $controller->getViewDir());
        $rname = igk_io_view_root_entry_uri($controller, $fname); 
        $params = $controller->getEnvParam(BaseController::VIEW_ARGS); 
        extract(array_merge(
            $controller->getSystemVars(),
            $controller->utilityViewArgs($fname, $file),
        ), EXTR_SKIP); 
        // igk_wln_e("the utils ",$controller->getSystemVars(), $controller->utilityViewArgs($fname, $file), $params);
         

        $controller->setEnvParam("fulluri", $furi);
        $params = isset($params) ? $params : array();
        $query_options = $controller->getEnvParam(IGK_VIEW_OPTIONS);
        $is_direntry = (count($params) == 0) && igk_str_endwith(explode('?', igk_io_request_uri())[0], '/');
        $controller->bindNodeClass($t, $fname, strtolower((isset($css_def) ? " " . $css_def : "")));
        $doc->body["class"] = "-custom-thumbnail";
        $doc->title = igk_configs()->website_title();
        $ob_level = ob_get_level();
        $controller->_get_extra_args($file);
        $g = Activator::CreateNewInstance(static::class, get_defined_vars());
        return $g; 
    }
    public function __isset($name)
    { 
        return property_exists($this, $name);
    }
    public function __toString(){
        return __CLASS__;
    }
}
<?php

// @author: C.A.D. BONDJE DOUE
// @filename: ViewEnvironmentArgs.php
// @date: 20220828 22:35:31
// @desc: 


namespace IGK\System;
/**
 * represent view environment args
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
}
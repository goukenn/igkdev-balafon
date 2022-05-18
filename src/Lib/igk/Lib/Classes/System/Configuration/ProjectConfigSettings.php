<?php

// @author: C.A.D. BONDJE DOUE
// @filename: SysConfigSettings.php
// @date: 20220509 08:42:59
// @desc: sys configuration settings


/**
 * general configuration setting. need to be activate from configs data expression
 * @package 
 */
class ProjectConfigSettings{

    /**
     * version of this controller
     * @var mixed
     */
    public $clVersion;
    /**
     * describe the controller
     * @var mixed
     */
    public $clDescription;

    /**
     * adapter name to use 
     * @var mixed
     */
    public $clDataAdapterName;
    /**
     * allow usage of data schema
     * @var mixed
     */
    public $clDataSchema;
    /**
     * name used to register
     * @var mixed
     */
    public $clRegisterName;
    /**
     * the index to use for target node
     * @var mixed
     */
    public $clTargetNodeIndex;
    /**
     * visibility on page
     * @var mixed
     */
    public $clVisiblePages;
    /**
     * application name
     * @var mixed
     */
    public $clAppName;
    /**
     * basic uri pattern
     * @var mixed
     */
    public $clBasicUriPattern;
    /**
     * schema's table prefix 
     * @var mixed
     */
    public $clDataTablePrefix;
    /**
     * disable auto cache view
     * @var mixed
     */
    public $no_auto_cache_view;
    /**
     * disable fallback to default action handle if no action found
     * @var mixed
     */
    public $no_fallback_to_default_action;
}

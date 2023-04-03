<?php

// @author: C.A.D. BONDJE DOUE
// @filename: SysConfigSettings.php
// @date: 20220509 08:42:59
// @desc: sys configuration settings
namespace IGK\System\Configuration;

use IGK\System\Configuration\Controllers\ConfigurationOptions;

/**
 * general configuration setting. need to be activate from configs data expression
 * @package 
 */
class ProjectConfigSettings extends ConfigurationOptions{
    /**
     * version of this controller
     * @var mixed
     */
    var $clVersion;
    /**
     * describe the controller
     * @var mixed
     */
    var $clDescription;

    /**
     * adapter name to use 
     * @var mixed
     */
    var $clDataAdapterName;
   
    /**
     * name used to register
     * @var mixed
     */
    var $clRegisterName;
    /**
     * the index to use for target node
     * @var mixed
     */
    var $clTargetNodeIndex;
    /**
     * visibility on page
     * @var mixed
     */
    var $clVisiblePages;
    /**
     * application name
     * @var mixed
     */
    var $clAppName; 
    /**
     * schema's table prefix 
     * @var mixed
     */
    var $clDataTablePrefix;
}

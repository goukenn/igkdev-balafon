<?php
// @author: C.A.D. BONDJE DOUE
// @file: ConfigurationOptions.php
// @date: 20221123 23:49:02
namespace IGK\System\Configuration\Controllers;

use IGK\Controllers\IControllerConfigurationData;

///<summary></summary>
/**
 * base application controller configuration options
 * @package IGK\System\Configuration\Controllers
 */
class ConfigurationOptions 
{
    /**
     * title in use
     * @var mixed
     */
    var $clTitle;
    /**
     * application name logic
     * @var ?string 
     */
    var $clAppName;
    /**
     * entry base uri 
     * @var ?string regext to entry route
     */
    var $clBasicUriPattern; 
    /**
     * application table prefix for database 
     * @var ?tring
     */
    var $clDataTablePrefix;
    /**
     * disable application by configuration
     * @var mixed
     */
    var $clAppNotActive;
    /**
     * enable or not the use of the data schema 
     * @var mixed
     */
    var $clDataSchema;
    /**
     * disable theme support rendering
     * @var mixed
     */
    var $no_theme_support;

    /**
     * theme list support by default null mean 'light' and 'dark' support
     * @var ?array|?string if string ',' separated list
     */
    var $theme_list;

    /**
     * disable auto caching view support. 
     * @var mixed
     */
    var $no_auto_cache_view;
    
    /**
     * disable fallback to DefaultAction handler in case of specific class not found.
     * @var ?bool
     */
    var $no_fallback_to_default_action;


}

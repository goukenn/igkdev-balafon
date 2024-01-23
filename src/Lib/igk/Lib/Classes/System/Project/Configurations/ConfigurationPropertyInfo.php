<?php
// @author: C.A.D. BONDJE DOUE
// @file: ConfigurationPropertyInfo.php
// @date: 20231219 09:31:45
namespace IGK\System\Project\Configurations;


///<summary></summary>
/**
* 
* @package IGK\System\Project\Configurations
*/
class ConfigurationPropertyInfo{
    /**
     * type value
     * @var string? 'bool' | 'text' | null
     */
    var $clType;
    var $clDefaultValue;
    var $clRequire;
}
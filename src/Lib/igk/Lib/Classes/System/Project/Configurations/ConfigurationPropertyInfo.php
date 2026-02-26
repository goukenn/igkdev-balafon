<?php
// @author: C.A.D. BONDJE DOUE
// @file: ConfigurationPropertyInfo.php
// @date: 20231219 09:31:45
namespace IGK\System\Project\Configurations;
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

    /**
    * auto generate doc.
    * @var mixed
    */
    var $clDefaultValue;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $clRequire;
}
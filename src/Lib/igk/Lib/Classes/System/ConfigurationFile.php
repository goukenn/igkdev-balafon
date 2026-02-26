<?php
// @author: C.A.D. BONDJE DOUE
// @file: ConfigurationFile.php
// @date: 20240816 08:05:07
namespace IGK\System;
use IGK\Constants;
use IGK\System\Configuration\EntityConfigurationSchema;

/**
* represent a project configuration file 
* @package IGK\System
* @author C.A.D. BONDJE DOUE
*/
class ConfigurationFile extends EntityConfigurationSchema{

    /**
    * auto generate doc.
    * @var mixed
    */
    const DEFAULT_MAINJS = 'default.js';

    /**
    * auto generate doc.
    * @var mixed
    */
    const CONFIG_FILE = Constants::PROJECT_CONF_FILE;
   
    /**
     * array of require module
     * @var ?string[]
     */
    var $required;
    /**
     * default entry 
     * @var mixed
     */
    var $mainJS;
    /**
     * configuration file workbench information 
     * @var ?ConfigurationWorkbenchInfo
     */
    var $workbench;
    /**
     * 
     * @var mixed
     */
    var $build;
    /**
     * 
     * @var mixed
     */
    var $exposedDir;
    /**
     * scripts configuration 
     * @var mixed
     */
    var $scripts;

    /**
     * default user profile
     * @var mixed
     */
    var $default_user_profile;
    /**
     * retrieve the main JS
     * @return mixed 
     */

    function getMainJS(){
        if ($this->mainJS){
            return $this->mainJS;
        }
        return self::DEFAULT_MAINJS;
    }
}
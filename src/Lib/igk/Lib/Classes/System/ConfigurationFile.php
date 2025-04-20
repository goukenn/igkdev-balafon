<?php
// @author: C.A.D. BONDJE DOUE
// @file: ConfigurationFile.php
// @date: 20240816 08:05:07
namespace IGK\System;

use IGK\Constants;

///<summary></summary>
/**
* represent a project configuration file 
* @package IGK\System
* @author C.A.D. BONDJE DOUE
*/
class ConfigurationFile{
    const DEFAULT_MAINJS = 'default.js';
    const CONFIG_FILE = Constants::PROJECT_CONF_FILE;
    /**
     * name of the project 
     */
    var $name;

    /**
     * description 
     * @var ?string
     */
    var $description;

    /**
     * 
     * @var ?string
     */
    var $version;

    /**
     * author name info
     * @var mixed
     */
    var $author;


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
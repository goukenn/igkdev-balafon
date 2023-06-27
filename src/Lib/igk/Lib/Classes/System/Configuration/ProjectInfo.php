<?php
// @author: C.A.D. BONDJE DOUE
// @file: ProjectInfo.php
// @date: 20230313 21:48:05
namespace IGK\System\Configuration;


///<summary></summary>
/**
* used to load project info setting  
* @package IGK\System\Configuration
*/
class ProjectInfo{
    const TYPE_PROJECT = 'project';
    var $name;
    var $base_dir;
    var $type = self::TYPE_PROJECT;     
    /**
     * 
     * @var ?ProjectConfiguration
     */
    var $configs;
    /**
     * 
     */
    var $settings;
    /**
     * json definition 
     * @var mixed
     */
    var $package_json;
    /**
     * composer setting packages 
     * @var mixed
     */
    var $composer;
    

}
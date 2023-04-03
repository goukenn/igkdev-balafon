<?php
// @author: C.A.D. BONDJE DOUE
// @file: ProjectInfo.php
// @date: 20230313 21:48:05
namespace IGK\System\Configuration;


///<summary></summary>
/**
* 
* @package IGK\System\Configuration
*/
class ProjectInfo{
    var $name;
    var $base_dir;
    var $type = "project";
    /**
     * array for module 
     * @var ?array
     */
    var $required;
    var $configs;
    var $settings;
    var $package_json;
    /**
     * composer setting packages 
     * @var mixed
     */
    var $composer;
    

}
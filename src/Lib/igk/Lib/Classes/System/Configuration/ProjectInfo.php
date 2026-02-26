<?php
// @author: C.A.D. BONDJE DOUE
// @file: ProjectInfo.php
// @date: 20230313 21:48:05
namespace IGK\System\Configuration;
/**
* used to load project info setting  
* @package IGK\System\Configuration
*/
class ProjectInfo{

    /**
    * auto generate doc.
    * @var mixed
    */
    const TYPE_PROJECT = 'project';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $name;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $base_dir;

    /**
    * auto generate doc.
    * @var mixed
    */
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
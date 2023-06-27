<?php
// @author: C.A.D. BONDJE DOUE
// @file: ProjectSettings.php
// @date: 20230418 11:33:39
namespace IGK\System\Configuration;


///<summary></summary>
/**
* reprensent project configuration setting
* @package IGK\System\Configuration
*/
class ProjectSettings{
    /**
     * current version
     * @var mixed
     */
    var $version = "1.0";
    /**
     * required module
     * @var mixed
     */
    var $required;
    /**
     * asset distribution folder
     * @var string
     */
    var $assetDist = "dist";
}
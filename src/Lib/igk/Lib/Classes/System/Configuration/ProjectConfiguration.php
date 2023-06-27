<?php
// @author: C.A.D. BONDJE DOUE
// @file: ProjectConfiguration.php
// @date: 20230414 14:16:49
namespace IGK\System\Configuration;


///<summary></summary>
/**
* represent project configuration 
* @package IGK\System\Configuration
*/
class ProjectConfiguration{
    /**
     * defined name
     * @var ?string
     */
    var $name;
    /**
     * list of required modules
     * @var ?array 
     */
    var $required;
    /**
     * project version
     * @var string
     */
    var $version = "1.0";
    /**
     * project author
     * @var string
     */
    var $author = IGK_AUTHOR; 
    /**
     * project description
     * @var descipriont
     */
    var $description;
    /**
     * exposed directories
     * @var ?array<string>
     */
    var $exposedDir;
}
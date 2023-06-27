<?php
// @author: C.A.D. BONDJE DOUE
// @file: ProjectSettings.php
// @date: 20230225 19:44:55
namespace IGK\Sync;


///<summary></summary>
/**
* sync project settings
* @package IGK\Sync
*/
class ProjectSettings{
    /**
     * ignore dirs list of directory to ignore for zip upload 
     * @var ?array
     */
    var $ignoredirs;

    /**
     * leave directory unchanged 
     * @var ?array
     */
    var $leavedirs;

    /**
     * list of directory to clear for every sync
     * @var ?array
     */
    var $cleardirs;
}
<?php
// @author: C.A.D. BONDJE DOUE
// @file: ApplicationModules.php
// @date: 20221108 15:59:43
namespace IGK\System\Controllers;

use IGKApp;

///<summary></summary>
/**
* 
* @package IGK\System\Controllers
*/
class ApplicationModules{
    /**
     * return 
     * @return null|string 
     */
    public static function GetCacheFile():?string{
        return IGKApp::IsInit() ? igk_io_cachedir()."/.modules.json" : null;
    }
}
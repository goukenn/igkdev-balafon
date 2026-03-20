<?php
// @author: C.A.D. BONDJE DOUE
// @file: ApplicationModules.php
// @date: 20221108 15:59:43
namespace IGK\System\Controllers;

use IGKApp;

/**
* managet application cache controller 
* @package IGK\System\Controllers
*/
abstract class ApplicationModules{
    /**
     * return cached file only if application is initialized
     * @return null|string 
     */
    public static function GetCacheFile():?string{
        return IGKApp::IsInit() ? igk_io_cachedir()."/.modules.json" : null;
    }
    /**
     * get require module caches 
     * @return string|null 
     */
    public static function GetSystemRequireCachedFile(){
          return IGKApp::IsInit() ? igk_io_cachedir()."/.modules-required.json" : null;

    }
    
}
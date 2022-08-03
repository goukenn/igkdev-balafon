<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ControllerPaths.php
// @date: 20220803 13:48:58
// @desc: 


namespace IGK\Controllers;

use IGK\Helper\StringUtility;

/**
 * 
 * @package IGK\Controllers
 */
class ControllerPaths{

    /**
     * collapse dir
     * @var mixed
     */
    var $dir;
    var $viewDir;
    var $stylesDir;
    var $dataDir;

    private function __construt(){

    }
    public static function Gets(BaseController $controller){
        $key = ControllerExtension::getEnvKey($controller, "path_info"); 

        if (null === ($inf = igk_environment()->get($key))){
            $dir = igk_io_expand_path(
                igk_io_collapse_path($controller->getDeclaredFileName())
            ); 
            $inf = new self();
            $inf->dir = $dir;
            $inf->viewDir = StringUtility::Uri(dirname($dir)."/".IGK_VIEW_FOLDER);
            $inf->stylesDir = StringUtility::Uri(dirname($dir)."/".IGK_STYLE_FOLDER);
            $inf->dataDir = StringUtility::Uri(dirname($dir)."/".IGK_STYLE_FOLDER);
           
            igk_environment()->set($key, $inf);
        }
        return $inf;
    }
}
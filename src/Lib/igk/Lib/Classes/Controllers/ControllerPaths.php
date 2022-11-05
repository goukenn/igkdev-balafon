<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ControllerPaths.php
// @date: 20220803 13:48:58
// @desc: 


namespace IGK\Controllers;

use IGK\Helper\StringUtility;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;
use IGKException;

/**
 * controller path management
 * @package IGK\Controllers
 */
class ControllerPaths{

    /**
     * collapse dir
     * @var mixed
     */
    var $dir;
    /**
     * view directory
     * @var mixed
     */
    var $viewDir;
    /**
     * styles directory
     * @var mixed
     */
    var $stylesDir;
    /**
     * data directory
     * @var mixed
     */
    var $dataDir;

    /**
     * script directory
     * @var mixed
     */
    var $scriptDir;

    private function __construt(){
    }
    /**
     * path environment handler
     * @param BaseController $controller 
     * @return mixed 
     * @throws NotFoundExceptionInterface 
     * @throws NotFoundExceptionInterface 
     * @throws ContainerExceptionInterface 
     * @throws IGKException 
     */
    public static function Gets(BaseController $controller){
        $key = ControllerExtension::getEnvKey($controller, "path_info"); 

        if (null === ($inf = igk_environment()->get($key))){
            $dir = $controller->getBaseDir() ?? dirname(igk_io_expand_path(
                igk_io_collapse_path($controller->getDeclaredFileName())
            )); 
            $inf = new self();
            $inf->dir = $dir;
            $inf->viewDir = StringUtility::Uri($dir."/".IGK_VIEW_FOLDER);
            $inf->stylesDir = StringUtility::Uri($dir."/".IGK_STYLE_FOLDER);
            $inf->dataDir = StringUtility::Uri($dir."/".IGK_DATA_FOLDER);
            $inf->scriptDir = StringUtility::Uri($dir."/".IGK_SCRIPT_FOLDER);           
            igk_environment()->set($key, $inf);
        }
        return $inf;
    }
}
<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKApplication.php
// @date: 20220803 13:48:54
// @desc: 

use IGK\ApplicationLoader;

require_once IGK_LIB_CLASSES_DIR.'/IGKApplicationBase.php';
/**
 * represent core application
 * @package 
 */
abstract class IGKApplication extends IGKApplicationBase{
    /**
     * create an application 
     * @param string $type 
     * @param mixed $bootoptions controller
     * @return mixed a create application
     * @throws TypeError 
     * @throws IGKException 
     */
    public static function Boot($type="web", $bootoptions=null, ?callable $boot=null){             
        $app = ApplicationLoader::Boot($type, $bootoptions);       
        // configure the create application 
        if ($app && $boot){
            //call before return
            $boot($app);
        }
        return $app;
    }
}
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
    const WEB_TYPE = 'web';
    const CRONTAB_TYPE  = 'crontab';
    const PHAR_TYPE = 'phar';
    const CSS_TYPE = 'css';
    const BALAFON_TYPE = 'balafon';
    const API_TYPE = 'api';
    const PHPUNIT_TYPE = 'phpunit';
    /**
    * create an application
    * @param string $type
    * @param mixed $bootoptions controller
    * @param ?callable $boot
    * @throws TypeError
    * @throws IGKException
    * @return mixed a create application
    */
    public static function Boot($type= self::WEB_TYPE, $bootoptions=null, ?callable $boot=null){             
        $app = ApplicationLoader::Boot($type, $bootoptions);       
        if ($app && $boot){
            // + | callback before return the application instance 
            $boot($app);
        }
        return $app;
    }
}
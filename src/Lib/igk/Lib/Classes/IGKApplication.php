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
    * auto generate doc.
    * @var mixed
    * @return void
    */
    const WEB_TYPE = 'web';
    /**
    * auto generate doc.
    * @var mixed
    * @return void
    */
    const CRONTAB_TYPE  = 'crontab';
    /**
    * auto generate doc.
    * @var mixed
    * @return void
    */
    const PHAR_TYPE = 'phar';
    /**
    * auto generate doc.
    * @var mixed
    * @return void
    */
    const CSS_TYPE = 'css';
    /**
    * auto generate doc.
    * @var mixed
    * @return void
    */
    const BALAFON_TYPE = 'balafon';
    /**
    * auto generate doc.
    * @var mixed
    * @return void
    */
    const API_TYPE = 'api';
    /**
    * auto generate doc.
    * @var mixed
    * @return void
    */
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
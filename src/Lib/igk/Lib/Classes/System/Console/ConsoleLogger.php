<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ConsoleLogger.php
// @date: 20220803 13:48:57
// @desc: primary console logger
namespace IGK\System\Console;
use IGK\Resources\R;

require_once __DIR__.'/IConsoleLogger.php';
/** @package IGK\System\Console */
/**
* auto generate doc.
* @package IGK\System\Console
*/
class ConsoleLogger implements IConsoleLogger{
    /**
    * Property: app.
    * @var mixed
    */
    var $app;
    /**
    * Property: private offscreen.
    * @var mixed
    */
    private $m_privateOffscreen;
    /**
    * .ctr
    * @param mixed $app
    */
    public function __construct($app)
    {
        $this->app = $app;
    }
    /**
    * Warn.
    * @param mixed $msg
    */
    public function warn($msg){
        $this->app->print_off($this->app::Gets(App::PURPLE, $msg));
    }
    /**
    * Danger.
    * @param mixed $msg
    */
    public function danger($msg){        
        $this->app->print_off($this->app::Gets(App::RED, $msg));
    }
    /**
    * Success.
    * @param mixed $msg
    */
    public function success($msg){
        $s = $this->app::Gets(App::GREEN, $msg); 
        $this->app->print($s);  
    }
    /**
    * Info.
    * @param mixed $msg
    */
    public function info($msg){
        $this->app->print($this->app::Gets(App::YELLOW, $msg));
    }
    /**
    * Logs.
    * @param mixed $msg
    */
    public function log($msg){
        $this->app->print($msg);
    }
    /**
    * auto generate doc.
    * @param mixed $msg
    * @return void
    */
    public function print($msg){
        $this->app->print($msg);
    }
    /**
    * Resources.
    * @param mixed $r
    */
    public function resources($r){
        static $tlang = null;
        if ($tlang === null){
            $tlang = self::get_lang();
        } 
       $c = empty($c = igk_getv($tlang, $r)) ? $r : $c; 
       return $c;
    }
    /**
    * auto generate doc.
    * @return
    */
    private static function get_lang(){
        $l = [];
            include(R::GetCurrentLangPath());
        return $l;
    }
    /**
    * Offscreen.
    * @return ?IConsoleLogger
    */
    public function offscreen() : ?IConsoleLogger{
        return $this->m_privateOffscreen ??($this->m_privateOffscreen = new ConsoleLoggerOffscreen($this));
    }
}
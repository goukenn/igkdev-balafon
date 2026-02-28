<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ConsoleLoggerOffscreen.php
// @date: 20240927 12:36:05
// @desc: offscreen to write 
namespace IGK\System\Console;

/**
* Console logger offscreen.
* @package IGK\System\Console
*/
class ConsoleLoggerOffscreen implements IConsoleLogger{

    /**
    * Property: parent.
    * @var mixed
    */
    private $parent;

    /**
    * Offscreen.
    */

    public function offscreen() { 
        return null;
    }

    /**
    * auto generate doc.
    * @return
    */
    private function getApp(){
        return $this->parent->app;
    }

    /**
    * .ctr
    * @param mixed $logger
    */
    public function __construct($logger)
    {
        $this->parent = $logger; 
    }

    /**
    * Logs.
    * @param mixed $msg
    */

    public function log($msg) { 
        $this->print( $msg);
    }

    /**
    * Info.
    * @param mixed $msg
    */

    public function info($msg) { 
        $this->print($this->getApp()::Gets(App::YELLOW, $msg));
    }

    /**
    * Warn.
    * @param mixed $msg
    */

    public function warn($msg) { 
        $this->print($this->getApp()::Gets(App::SHA_INDIGO, $msg));
    }

    /**
    * Success.
    * @param mixed $msg
    */

    public function success($msg) { 
        $this->print($this->getApp()::Gets(App::GREEN, $msg));
    }

    /**
    * Danger.
    * @param mixed $msg
    */

    public function danger($msg){
        $this->print($this->getApp()::Gets(App::RED, $msg));
    }

    /**
    * Prints.
    * @param mixed $msg
    */

    public function print($msg){
        if (defined('STDERR')){
            fwrite(\STDERR, $msg."\n");
        } else {
            $offscreen = igk_environment()->logoffscreen  ?? [];
            $offscreen[] = $msg."\n";
            igk_environment()->logoffscreen = $offscreen;
        }
    }
}
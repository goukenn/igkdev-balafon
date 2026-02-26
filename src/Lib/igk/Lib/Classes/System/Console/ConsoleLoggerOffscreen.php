<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ConsoleLoggerOffscreen.php
// @date: 20240927 12:36:05
// @desc: offscreen to write 
namespace IGK\System\Console;

/**
* auto generate doc.
* @package IGK\System\Console
*/
class ConsoleLoggerOffscreen implements IConsoleLogger{

    /**
    * auto generate doc.
    * @var mixed
    */
    private $parent;

    /**
    * auto generate doc.
    */

    public function offscreen() { 
        return null;
    }
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
    * auto generate doc.
    * @param mixed $msg
    */

    public function log($msg) { 
        $this->print( $msg);
    }

    /**
    * auto generate doc.
    * @param mixed $msg
    */

    public function info($msg) { 
        $this->print($this->getApp()::Gets(App::YELLOW, $msg));
    }

    /**
    * auto generate doc.
    * @param mixed $msg
    */

    public function warn($msg) { 
        $this->print($this->getApp()::Gets(App::SHA_INDIGO, $msg));
    }

    /**
    * auto generate doc.
    * @param mixed $msg
    */

    public function success($msg) { 
        $this->print($this->getApp()::Gets(App::GREEN, $msg));
    }

    /**
    * auto generate doc.
    * @param mixed $msg
    */

    public function danger($msg){
        $this->print($this->getApp()::Gets(App::RED, $msg));
    }

    /**
    * auto generate doc.
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
<?php

// @author: C.A.D. BONDJE DOUE
// @filename: ConsoleLoggerOffscreen.php
// @date: 20240927 12:36:05
// @desc: offscreen to write 

namespace IGK\System\Console;

class ConsoleLoggerOffscreen implements IConsoleLogger{
    private $parent;
    private function getApp(){
        return $this->parent->app;
    }
    public function __construct($logger)
    {
        $this->parent = $logger; 
      
    }
    public function log($msg) { 
        $this->print( $msg);
    }

    public function info($msg) { 
        $this->print($this->getApp()::Gets(App::YELLOW, $msg));
    }

    public function warn($msg) { 
        $this->print($this->getApp()::Gets(App::SHA_INDIGO, $msg));
    }

    public function success($msg) { 
        $this->print($this->getApp()::Gets(App::GREEN, $msg));
    }
    public function danger($msg){
        $this->print($this->getApp()::Gets(App::RED, $msg));
    }

    public function print($msg){
        fwrite(STDERR, $msg."\n");
    }

}
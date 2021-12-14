<?php

namespace IGK\System\Console;

use IGK\Resources\R;

class ConsoleLogger{
    var $app; 
    public function __construct($app)
    {
        $this->app = $app;
    }
    public function warn($msg){
        $this->app->print($this->app::gets(App::PURPLE, $msg));
    }
    public function danger($msg){
        $this->app->print($this->app::gets(App::RED, $msg));
    }
    public function success($msg){
        $this->app->print($this->app::gets(App::GREEN, $msg));
    }
    public function info($msg){
        $this->app->print($this->app::gets(App::YELLOW, $msg));
    }
    public function log(...$msg){
        $this->app->print(...$msg);
    }
    public function print(...$msg){
        $this->app->print(...$msg);
    }

    public function resources($r){
        static $tlang = null;
        if ($tlang === null){
            $tlang = self::get_lang();
        } 
       $c = empty($c = igk_getv($tlang, $r)) ? $r : $c; 
       return $c;
    }
    private static function get_lang(){
        $l = [];
            include(R::GetCurrentLangPath());
        return $l;
    }
}
<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ConsoleLogger.php
// @date: 20220803 13:48:57
// @desc: 


namespace IGK\System\Console;

use IGK\Resources\R;

class ConsoleLogger{
    var $app; 
    public function __construct($app)
    {
        $this->app = $app;
    }
    public function warn($msg){
        $this->app->print_off($this->app::Gets(App::PURPLE, $msg));
    }
    public function danger($msg){
        if (strstr($msg,'Duplicate entry')){
            //igk_wln_e("stop : Duplicate entry", $msg);
        }
        $this->app->print_off($this->app::Gets(App::RED, $msg));
    }
    public function success($msg){
        $this->app->print_off($this->app::Gets(App::GREEN, $msg));
    }
    public function info($msg){
        $this->app->print_off($this->app::Gets(App::YELLOW, $msg));
    }
    public function log(...$msg){
        $this->app->print_off(...$msg);
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
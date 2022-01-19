<?php
// @file: Benchmark.php
// @author: CA.D.D BONDJE DOUE
// @date : 20220112
namespace IGK\System\Diagnostics;

use stdClass;

/**
 * represent balafon benchmark
 * @package IGK\System\Diagnostics
 */
class Benchmark{
    private static $sm_instance;
    public static $Enabled;
    private $mark = [];
    private $m_configs;
    private function __construct(){  
        $this->m_configs = new stdClass();
        $this->m_configs->dieOnError = false;
    }
    public static function getInstance(){
        if (self::$sm_instance === null)
            self::$sm_instance = new self();
        return self::$sm_instance;
    }
    public static function expect($name, $duration){
        if (!self::$Enabled){
            return;
        } 

        $m = & self::getInstance()->mark;
        if (isset($m[$name])){
            $time = igk_sys_request_time();
            $v_duration = ($time - $m[$name]);
            if ($v_duration > $duration){
                $msg = sprintf("Benchmark:%s request time exceed %s . duration : %s ", $name, $duration, $v_duration);
                igk_environment()->write_debug("<div class='igk-danger'>".$msg."</div>");
                if(self::getInstance()->m_configs->dieOnError){
                    die($msg);
                }
            } 
            unset($m[$name]);
        }
    }
    public static function mark($name){
        self::getInstance()->mark[$name] = igk_sys_request_time();
    }
    /**
     * set die on error
     * @param bool $b 
     * @return void 
     */
    public function dieOnError(bool $b){
        $this->m_configs->dieOnError = $b;
    }
}
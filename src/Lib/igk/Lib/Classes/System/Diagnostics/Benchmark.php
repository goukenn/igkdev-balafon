<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Benchmark.php
// @date: 20220803 13:48:56
// @desc: 

// @file: Benchmark.php
// @author: CA.D.D BONDJE DOUE
// @date : 20220112
namespace IGK\System\Diagnostics;

use IGKException;
use stdClass;

/**
 * represent balafon benchmark
 * @package IGK\System\Diagnostics
 */
class Benchmark{
    private static $sm_instance;
    public  static $Enabled;
    private $mark = [];
    private $m_configs;
    private function __construct(){  
        $this->m_configs = new BenchmarkOptions();
        $this->m_configs->dieOnError = false;
    }
    public static function getInstance(){
        if (self::$sm_instance === null)
            self::$sm_instance = new self();
        return self::$sm_instance;
    }
    /**
     * activate diagnostics
     * @param bool $enabled 
     * @param null|array $options {dieOnError:bool}
     * @return void 
     * @throws IGKException 
     */
    public static function Activate(bool $enabled, ?array $options=null){
        self::$Enabled = $enabled;
        if ($options){
        $i = self::getInstance();
        $i->m_configs->dieOnError = igk_getv($options, "dieOnError", false);
        }
    }
    /**
     * bech mark expectation
     * @param string $name identifier
     * @param float $duration duration
     * @return void 
     */
    public static function expect(string $name, float $duration, ?string $message=null){
        if (!self::$Enabled){
            return;
        } 
        $v_i = self::getInstance();

        $m = & $v_i->mark;
        if (isset($m[$name])){
            $time = igk_sys_request_time();
            $v_duration = ($time - $m[$name]);
            if ($v_duration > $duration){
                $msg = sprintf("Benchmark:%s request time exceed %s . duration : %s ", $name, $duration, $v_duration);
                igk_environment()->write_debug("<div class='igk-danger'>".$msg."</div>");
                if ($message){
                    $msg.="\n".$message;
                }
                if($v_i->m_configs->dieOnError){                   
                    $s = igk_ob_trace();
                    die(igk_ob_get_func("igk_html_pre",[
                        $msg,
                        "\nServerRequestTime : ".igk_sys_request_time(),
                        $s
                    ]));
                }
            } 
            unset($m[$name]);
        }
    }
    /**
     * 
     * @param string $name measure if enabled
     * @param bool $unset unset the mark measure
     * @return int|float|void 
     */
    public static function measure(string $name, bool $unset = false){
        if (!self::$Enabled){
            return;
        }  
        $m = & self::getInstance()->mark;
        if (isset($m[$name])){
            $time = igk_sys_request_time();
            $v_duration = ($time - $m[$name]);
            if ($unset){
                unset($m[$name]);
            }
            return $v_duration;
        }
    }
    /**
     * mark to bench mark
     * @param mixed $name 
     * @return void 
     */    
    public static function mark($name){
        self::getInstance()->mark[$name] = igk_sys_request_time();
    }
    /**
     * write to - if enabled
     * @param mixed $args 
     * @return void 
     * @throws IGKException 
     */
    public static function write(...$args){
        if (self::$Enabled){
            igk_wln($args);
        }
    }
    public static function log(...$args){
        if (self::$Enabled){
            igk_ilog($args);
        }
    }
    /**
     * set die on error
     * @param bool $b 
     * @return void 
     */
    public function dieOnError(bool $b){
        $this->m_configs->dieOnError = $b;
    }
    public static function __callStatic($name, $arguments)
    {
        if (method_exists(static::class, $m = "Set".$name)){
            return self::$m(...$arguments);
        }
    }
     
}
<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKLog.php
// @date: 20220803 13:48:54
// @desc: 



///<summary>Represente class: IGKLog</summary>

use IGK\Database\DataAdapterBase;
use IGK\Helper\ExceptionUtils;
use IGK\Helper\IO;
use IGK\Helper\SysUtils;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\NotImplementException;
 

/**
 * Represente IGKLog class
 */
final class IGKLog extends IGKObject
{ 
    /**
     * logger flags
     * @var false
     */
    private static $sm_loggin = false;
    private static $sm_instance;
    ///<summary></summary>
    /**
     * 
     */
    private function __construct()
    {
    }
    ///<summary></summary>
    /**
     * 
     */
    public function ClearLog()
    {
        $f = $this->getLogFile();
        $r = fopen($f, "w+");
        fclose($r);
    }
    public function write_i_data(){
        throw new NotImplementException(__METHOD__);
    }
    ///<summary></summary>
    /**
     * @return static
     */
    public static function getInstance()
    {
        // if (!isset($_SESSION)) {
        //     igk_die("/|\ must start session");
        // }
        if (self::$sm_instance == null) {
            self::$sm_instance = igk_get_class_instance(__CLASS__, function () {
                return new IGKLog();
            });
        }
        return self::$sm_instance;
    }
    /**
     * get log file in use 
     * @var GetLogFile
     */
    public static function GetSystemLogFile():string {
        $f = igk_environment()->get("logfile", igk_const("IGK_LOG_FILE"));
        if (!$f) {
            $f = \IGKLog::getInstance()->getLogFile();
        }
        return $f;
    }
    ///<summary>write log to IGK_LOG_FILE</summary>
    /**
     * write log to IGK_LOG_FILE
     * @param mixed $msg
     * @param mixed $tag
     * @param mixed $traceindex
     */
    public static function Append(string $msg, ?string $tag = null,int $traceindex = 0, bool $dblog=true)
    {
        if (self::$sm_loggin){          
            return;
        }
        self::$sm_loggin = true;

        if (!defined('IGK_NO_TRACELOG')) {
            if (!igk_sys_env_production()) {
                igk_ilog_trace(igk_trace_function(2 + $traceindex));
                $msg = array("msg" => $msg, "trace" => igk_ilog_get_trace());
            }
        }  
        if (empty($tag)) {
            $tag = IGK_LOG_SYS;
        } 
        $f = self::GetSystemLogFile();
       
        igk_log_append($f, $msg, $tag);
        
        if (is_array($msg)) {
            $s = "Array(" . count($msg) . "):[\n";
            foreach ($msg as $k => $v) {
                $s .= $k . ":";
                if (is_array($v)) {
                    $s .= "Array";
                } else if (is_object($v))
                    $s .= get_class($v);
                else
                    $s .= $v;
                $s .= "\n";
            }
            $s .= "]";
            $msg = $s;
        }
        
        if (igk_environment()->isDev()) {
            error_log("[{$tag}] - $msg");
        }

        // + | ---------------------------------------------------
        // + | log running data to running app
        // + |
        try{
            self::WriteDbLog($msg, $tag, $dblog);
        } catch(Exception $ex){
            // possibility of missing db log 
        }
        igk_hook(IGKEvents::HOOK_LOG_APPEND, func_get_args());
        self::$sm_loggin = false;
    }
    /**
     * 
     * @param mixed $msg 
     * @param mixed $tag 
     * @param mixed $dblog 
     * @return void 
     * @throws IGKException 
     * @throws EnvironmentArrayException 
     * @throws Exception 
     * @throws CssParserException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function WriteDbLog($msg, $tag, $dblog){
        if ($dblog && self::CanDBLog()){  
            try{          
                \IGK\Models\DbLogs::create([
                    'db_logs_msg'=>$msg,
                    'db_logs_tag'=>$tag,
                    'db_logs_status'=>0
                ], false, false);            
            } catch(TypeError $ex ){
                igk_dev_wln_e(__FILE__.":".__LINE__,  $ex->getMessage());
            }
            catch(Exception $ex ){ 
                throw $ex;
            }
        }
    }
    /**
     * check if can write log to database
     * @return void|false 
     * @throws IGKException 
     */
    public static function CanDBLog(){
        if (defined("IGK_TEST_INIT")){
            return false;
        }
        $g = !igk_environment()->NO_DB_LOG && !igk_configs()->no_db_log && igk_app();
        if ($g){
            $db = igk_configs()->get("default_dataadapter");
            return $db && DataAdapterBase::IsRegister($db);
        }
        return false;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getLogFile()
    {  
        return igk_getv(igk_configs(), "LogFile") ?? $this->getDefaultLogFile(); 
    }
    public function getDefaultLogFile(){
        return igk_io_cachedir()."/Data/Logs/.global." . igk_environment()->getToDay() . ".log"; 
    }
    public function getDefaultErrorLogFile(){
        return igk_io_cachedir()."/Data/Logs/.global-error." . igk_environment()->getToDay() . ".log"; 
    }
    ///<summary></summary>
    ///<param name="msg"></param>
    /**
     * 
     * @param mixed $msg
     */
    public function write($msg)
    {
        $this->write_i("IGKLOG", $msg);
    }
    ///<summary></summary>
    ///<param name="tag"></param>
    ///<param name="message"></param>
    ///<param name="eval" default="1"></param>
    /**
     * 
     * @param mixed $tag
     * @param mixed $message
     * @param mixed $eval the default value is 1
     */
    public function write_i($tag, $message, $eval = 1)
    {
        $f = $this->getLogFile();
        if (empty($f)) {
            return;
        }
        if (is_array($message) && $eval) {
            IGKOb::Start();
            igk_dump_array($message);
            $c = IGKOb::Content();
            IGKOb::Clear();
            $message = "Array[" . $c . "]";
        }
        $r = null;
        if (!file_exists($f) && !IO::CreateDir(dirname($f)))
            return;
        $r = @fopen($f, file_exists($f) ? "a+" : "w+");
        if (is_array($message)) {
            IGKOb::Start();
            var_dump($message);
            $message = IGKOb::Content();
            IGKOb::Clear();
        }
        if ($r) {
            fwrite($r, date("h:i:s") . ": - [" . $tag . "] - " . $message . IGK_LF);
            fclose($r);
        }
    }
}

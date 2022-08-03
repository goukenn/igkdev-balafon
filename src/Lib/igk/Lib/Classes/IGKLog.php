<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKLog.php
// @date: 20220803 13:48:54
// @desc: 



///<summary>Represente class: IGKLog</summary>

use IGK\Database\DataAdapterBase;
use IGK\Helper\IO;
use IGK\System\Exceptions\NotImplementException;

/**
 * Represente IGKLog class
 */
final class IGKLog extends IGKObject
{
    // const ERRORLOGFILE = "Data/Logs/.global-error." . IGK_TODAY . ".log";
    // const LOGFILE = "Data/Logs/.global." . IGK_TODAY . ".log";
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
    ///<summary>write log to IGK_LOG_FILE</summary>
    /**
     * write log to IGK_LOG_FILE
     * @param mixed $msg
     * @param mixed $tag
     * @param mixed $traceindex
     */
    public static function Append($msg, $tag = null, $traceindex = 0)
    {
        if (self::$sm_loggin){
            igk_die("try to log when appending...log message");
        }
        self::$sm_loggin = true;
        // + igk_wln($msg);
        // + igk_trace();
        // + igk_exit();
        if (!defined('IGK_NO_TRACELOG')) {
            if (!igk_sys_env_production()) {
                igk_ilog_trace(igk_trace_function(2 + $traceindex));
                $msg = array("msg" => $msg, "trace" => igk_ilog_get_trace());
            }
        }


       
        $f = "";
        if (!($f = igk_const("IGK_LOG_FILE")))
            $f = igk_ilog_file();
       
        if (empty($tag)) {
            $tag = IGK_LOG_SYS;
        } 
        igk_log_append($f, $msg, $tag);
        
        if (is_array($msg)) {
            $s = "Array(" . count($msg) . "):[\n";
            $o = "";
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

        // +| ---------------------------------------------------
        // +| log running data to running app
       
        if (self::CanDBLog()){            
            \IGK\Models\DbLogs::add($msg, $tag, 0);            
        }
        igk_hook(IGKEvents::HOOK_LOG_APPEND, func_get_args());
        self::$sm_loggin = false;
    }
    /**
     * check if can write log to database
     * @return void|false 
     * @throws IGKException 
     */
    public static function CanDBLog(){
        $g = !igk_environment()->get("NoDBLog") && !igk_configs()->no_db_log && igk_app();
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
        return igk_getv(igk_configs(), "LogFile", $this->getDefaultLogFile()); 
    }
    public function getDefaultLogFile(){
        return igk_io_applicationdir()."/Data/Logs/.global." . igk_environment()->getToDay() . ".log"; 
    }
    public function getDefaultErrorLogFile(){
        return igk_io_applicationdir()."/Data/Logs/.global-error." . igk_environment()->getToDay() . ".log"; 
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
